<?php
/**
 * What a JSON calculator definition is allowed to be.
 *
 * This route writes a whole working tool onto a public site without anybody
 * reviewing a diff first, so the validator is the only thing standing between
 * a malformed definition and a broken page. Each check below names the failure
 * it is there to prevent, because a test whose purpose nobody remembers is a
 * test somebody eventually deletes.
 *
 * Usage: php tests/test-json-calculators.php
 */

require_once __DIR__ . '/bootstrap.php';

$failures = array();
$checks   = 0;

function check( $label, $condition, $detail = '' ) {
	global $failures, $checks;
	$checks++;
	if ( ! $condition ) {
		$failures[] = $label . ( $detail ? ': ' . $detail : '' );
	}
}

/** Asserts a definition is refused, and that the refusal says something useful. */
function refuses( $label, $definition, $expected_fragment ) {
	global $failures, $checks;
	$checks++;

	$result = Calculatorr_Json_Calculators::validate( $definition );

	if ( ! is_wp_error( $result ) ) {
		$failures[] = $label . ': it was accepted';
		return;
	}

	$message = $result->get_error_message();

	if ( false === stripos( $message, $expected_fragment ) ) {
		$failures[] = sprintf( '%s: refused, but the reason was "%s" rather than something about "%s"', $label, $message, $expected_fragment );
	}
}

/** A definition that should pass, which every test below varies one part of. */
function sound( $overrides = array() ) {
	return array_merge(
		array(
			'slug'        => 'test-json-calculator',
			'title'       => 'Test Calculator',
			'category'    => 'math',
			'description' => 'A calculator that exists only so the validator has something to chew on.',
			'fields'      => array(
				array( 'id' => 'amount', 'label' => 'Amount', 'type' => 'number', 'default' => '10' ),
			),
			'formula'       => 'return { label: "Doubled", value: decimals( num( v.amount ) * 2, 2 ) };',
			'default_result' => array( 'label' => 'Doubled', 'value' => '20' ),
		),
		$overrides
	);
}

/* ---------- The sound one has to pass, or nothing below means anything ---------- */

$ok = Calculatorr_Json_Calculators::validate( sound() );

check( 'a sound definition is accepted', ! is_wp_error( $ok ), is_wp_error( $ok ) ? $ok->get_error_message() : '' );

if ( ! is_wp_error( $ok ) ) {
	check( 'the formula is stored exactly as written', $ok['formula'] === sound()['formula'] );
	check( 'the field survives with its default', '10' === $ok['fields'][0]['default'] );
	check( 'the worked answer survives', '20' === $ok['default_result']['value'] );
}

/* ---------- Shape ---------- */

refuses( 'a key nobody recognises', sound( array( 'colour' => 'blue' ) ), 'unexpected' );
refuses( 'an uppercase slug', sound( array( 'slug' => 'Test-Calculator' ) ), 'slug' );
refuses( 'a slug with a double hyphen', sound( array( 'slug' => 'test--calculator' ) ), 'slug' );
refuses( 'no title', sound( array( 'title' => '' ) ), 'title' );
refuses( 'no description', sound( array( 'description' => '' ) ), 'description' );
refuses( 'a category that does not exist', sound( array( 'category' => 'astrology' ) ), 'category' );

/*
 * A JSON definition taking a slug that ships as PHP would load, lose to the
 * file in the registry, and sit in the database looking published while doing
 * nothing at all. Being told no is better than that.
 */
refuses( 'a slug that already ships as PHP', sound( array( 'slug' => 'bmi-calculator' ) ), 'ships' );

/* ---------- Fields ---------- */

refuses( 'no fields at all', sound( array( 'fields' => array() ) ), 'fields' );
refuses( 'a field with no label', sound( array( 'fields' => array( array( 'id' => 'a', 'type' => 'number' ) ) ) ), 'label' );
refuses(
	'a field type the renderer cannot draw',
	sound( array( 'fields' => array( array( 'id' => 'a', 'label' => 'A', 'type' => 'colour' ) ) ) ),
	'type'
);

/*
 * The id becomes a property name the formula reads as v.<id>, so anything that
 * is not a plain identifier produces a field the formula has no way to name.
 */
refuses(
	'an id that is not a usable property name',
	sound( array( 'fields' => array( array( 'id' => 'my-field', 'label' => 'A', 'type' => 'number' ) ) ) ),
	'id'
);

refuses(
	'a choice field with no options',
	sound( array( 'fields' => array( array( 'id' => 'units', 'label' => 'Units', 'type' => 'segmented' ) ) ) ),
	'options'
);

/*
 * A default that is not one of the options renders a control with nothing
 * selected, and the formula then receives a value no branch of it expects.
 */
refuses(
	'a default outside its own options',
	sound( array( 'fields' => array( array(
		'id'      => 'units',
		'label'   => 'Units',
		'type'    => 'select',
		'options' => array( 'metric' => 'Metric', 'imperial' => 'Imperial' ),
		'default' => 'nautical',
	) ) ) ),
	'options'
);

/*
 * Two fields may share an id on purpose, which is how the metric and imperial
 * versions of one measurement feed a single name into the formula. What they
 * may not do is share it while both are on screen, because then one of them is
 * furniture and nobody can tell which.
 */
refuses(
	'two visible fields sharing an id',
	sound( array( 'fields' => array(
		array( 'id' => 'weight', 'label' => 'Weight', 'type' => 'number', 'default' => '70' ),
		array( 'id' => 'weight', 'label' => 'Weight again', 'type' => 'number', 'default' => '80' ),
	) ) ),
	'already used'
);

$paired = Calculatorr_Json_Calculators::validate( sound( array( 'fields' => array(
	array( 'id' => 'units', 'label' => 'Units', 'type' => 'segmented', 'options' => array( 'metric' => 'Metric', 'imperial' => 'Imperial' ), 'default' => 'metric' ),
	array( 'id' => 'weight', 'label' => 'Weight', 'type' => 'number', 'default' => '70', 'show_when' => array( 'units' => 'metric' ) ),
	array( 'id' => 'weight', 'label' => 'Weight', 'type' => 'number', 'default' => '154', 'show_when' => array( 'units' => 'imperial' ) ),
) ) ) );

check( 'two fields sharing an id under different conditions are fine', ! is_wp_error( $paired ),
	is_wp_error( $paired ) ? $paired->get_error_message() : '' );

/* ---------- The formula ---------- */

refuses( 'no formula', sound( array( 'formula' => '' ) ), 'formula' );
refuses( 'a formula that never returns', sound( array( 'formula' => 'var x = num( v.amount ) * 2;' ) ), 'return' );

/*
 * The sandbox is what actually stops a formula reaching the network, since it
 * has had fetch and the rest taken away before the body is compiled. Naming
 * them here is a second line, and mostly it is a courtesy: a formula written
 * against the wrong mental model gets told so at the point of writing instead
 * of failing silently in front of a visitor.
 */
foreach ( array( 'fetch', 'XMLHttpRequest', 'document', 'localStorage', 'eval' ) as $name ) {
	refuses(
		'a formula naming ' . $name,
		sound( array( 'formula' => 'var x = ' . $name . '; return { value: "1" };' ) ),
		$name
	);
}

check(
	'a formula whose own variable merely contains a banned word is fine',
	! is_wp_error( Calculatorr_Json_Calculators::validate( sound( array(
		'formula' => 'var documentCount = num( v.amount ); return { label: "Docs", value: String( documentCount ) };',
	) ) ) )
);

/* ---------- The worked answer ---------- */

/*
 * This is what a crawler indexes and what a visitor reads in the moment before
 * the sandbox answers, so a definition without one would publish a page whose
 * headline figure was a dash until JavaScript arrived.
 */
refuses( 'no worked answer', sound( array( 'default_result' => array() ) ), 'default_result' );
refuses( 'a worked answer with no value', sound( array( 'default_result' => array( 'label' => 'Doubled' ) ) ), 'default_result' );

/* ---------- Content ---------- */

refuses(
	'a table whose rows are a different width from its header',
	sound( array( 'explainer' => array( array(
		'heading' => 'A table',
		'body'    => 'Some words about it.',
		'table'   => array( 'head' => array( 'A', 'B' ), 'rows' => array( array( '1', '2' ), array( '3' ) ) ),
	) ) ) ),
	'cells'
);

refuses(
	'a question with no answer',
	sound( array( 'faqs' => array( array( 'q' => 'Why?' ) ) ) ),
	'q and an a'
);

/* ---------- Storing and reading back ---------- */

$store = Calculatorr_Json_Calculators::instance();
$saved = $store->save( sound() );

check( 'a sound definition saves', ! is_wp_error( $saved ), is_wp_error( $saved ) ? $saved->get_error_message() : '' );
check( 'it reads back', null !== $store->get( 'test-json-calculator' ) );
check( 'it reports itself as a database copy', 'db' === $store->source_of( 'test-json-calculator' ) );
check( 'its formula is what the renderer will print', false !== strpos( $store->formula( 'test-json-calculator' ), 'Doubled' ) );

check( 'a definition the validator refuses never reaches the option row',
	is_wp_error( $store->save( sound( array( 'category' => 'astrology' ) ) ) ) );

$registry = Calculatorr_Registry::instance();
$registry->reload();
$merged = $registry->get( 'test-json-calculator' );

check( 'the registry serves it like any other calculator', is_array( $merged ) );

if ( is_array( $merged ) ) {
	/* The point of merging rather than keeping a separate list: by the time
	   anything downstream sees a config, the defaults the PHP files get have
	   been applied to this one too. */
	check( 'it was given the meta title every config gets', 'Test Calculator - Free Online Tool' === $merged['meta_title'] );
	check( 'it was given an h1 from its title', 'Test Calculator' === $merged['h1'] );
	check( 'it kept its own worked answer', '20' === $merged['default_result']['value'] );
}

check( 'removing it reports that there was something to remove', true === $store->remove( 'test-json-calculator' ) );
check( 'removing it twice does not pretend', false === $store->remove( 'test-json-calculator' ) );

$registry->reload();
check( 'it is gone from the registry', null === $registry->get( 'test-json-calculator' ) );

/* ---------- Report ---------- */

echo "\n" . $checks . " checks\n\n";

if ( $failures ) {
	echo implode( "\n", $failures ) . "\n\n";
	echo count( $failures ) . " failures\n";
	exit( 1 );
}

echo "0 failures\n";
