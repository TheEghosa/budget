/*
 * Where a JSON-defined calculator's formula runs.
 *
 * A calculator that ships as a PHP config plus a function in formulas.js is
 * reviewed and deployed as code. A calculator defined in JSON is not: it can be
 * written straight into the database over the REST route, and although only an
 * administrator can do that, "only an administrator" is a policy control rather
 * than a technical one. So the formula runs here instead of on the page, inside
 * a worker that has no DOM at all and has had every way out to the network
 * taken off it before the first line of formula code is compiled.
 *
 * What it can still do is arithmetic: loops, conditionals, Math, strings, dates
 * and the whole shared helper kit, which is everything the hundred and five
 * shipped formulas use. That is deliberate, because the point of the exercise
 * is that a JSON calculator computes exactly like a coded one.
 *
 * The worker answers messages of the shape { id, slug, source, values } and
 * replies with { id, ok, result } or { id, ok: false, error }. The source
 * travels with every run rather than being registered once, so a worker that
 * has just been terminated for running too long can be replaced and used again
 * without the page having to re-register anything.
 */
( function () {
	'use strict';

	/* Both files have to come in before the door is shut, since importScripts
	   is one of the things being taken away. The query string is carried across
	   so the worker and the page cache-bust on the same plugin version. */
	var version = self.location.search || '';
	importScripts( 'formula-kit.js' + version, 'formula-runner.js' + version );

	var KIT = self.CalculatorrKit;
	var RUNNER = self.CalculatorrRunner;
	var post = self.postMessage.bind( self );

	/**
	 * Removes a global outright.
	 *
	 * Some of these are non-configurable accessors depending on the browser, so
	 * failing to remove one is expected rather than exceptional and must not
	 * stop the rest going. Whatever survives is named in the ready message,
	 * which is how a browser that will not let go of fetch becomes visible in
	 * the console rather than quietly trusted.
	 */
	function deny( name ) {
		try {
			Object.defineProperty( self, name, {
				value: undefined,
				writable: false,
				configurable: false,
				enumerable: false
			} );
		} catch ( error ) {
			try {
				self[ name ] = undefined;
			} catch ( ignored ) {
				return false;
			}
		}

		return ! self[ name ];
	}

	var SEALED = [
		/* Every route off the machine. */
		'fetch', 'XMLHttpRequest', 'WebSocket', 'EventSource', 'importScripts',
		'navigator', 'Request', 'Response', 'Headers',
		/* Anything that could start a second context and inherit more than
		   this one has. */
		'Worker', 'SharedWorker', 'BroadcastChannel', 'MessageChannel',
		/* Storage, which is not needed to divide two numbers and is a place a
		   formula could otherwise leave something behind between visits. */
		'indexedDB', 'caches', 'localStorage', 'sessionStorage',
		/* Odds and ends that reach outside the worker one way or another. */
		'createImageBitmap', 'Notification', 'open', 'close', 'reportError'
	];

	var survivors = SEALED.filter( function ( name ) {
		return ! deny( name ) && typeof self[ name ] !== 'undefined';
	} );

	var compiled = null;

	self.onmessage = function ( event ) {
		var job = event.data || {};

		try {
			if ( ! compiled || compiled.source !== job.source ) {
				compiled = { source: job.source, fn: RUNNER.compile( job.source || '', KIT ) };
			}

			post( { id: job.id, ok: true, result: RUNNER.clean( compiled.fn( job.values || {}, KIT ) ) } );
		} catch ( error ) {
			/* A formula that will not compile is thrown away rather than kept,
			   so the next edit of it gets a fresh attempt instead of failing
			   forever against a cached wreck. */
			compiled = null;
			post( { id: job.id, ok: false, error: ( error && error.message ) ? String( error.message ) : String( error ) } );
		}
	};

	post( { ready: true, survivors: survivors } );
}() );
