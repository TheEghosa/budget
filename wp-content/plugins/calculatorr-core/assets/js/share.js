/*
 * Sharing: a restorable link, a rendered snapshot, and the platform hand-offs.
 *
 * The snapshot is drawn onto a canvas here in the browser rather than
 * screenshotted from the DOM. Screenshotting libraries are large, slow and
 * unreliable with web fonts, and this site's whole proposition is that it loads
 * fast, so a hand-drawn card is both smaller and more predictable. Nothing is
 * uploaded anywhere: the image is made on the device and stays there unless the
 * person chooses to send it.
 */
( function () {
	'use strict';

	var CARD_W = 1200;
	var CARD_H = 630;

	var INK = '#14181D';
	var MUTED = '#5A6472';
	var PAPER = '#FBFAF8';
	var TEAL = '#0E6E63';
	var AMBER = '#B45E0C';
	var LINE = '#E4E2DC';

	var DISPLAY = "'Space Grotesk', 'Helvetica Neue', Helvetica, Arial, sans-serif";
	var BODY = "'Source Sans 3', 'Helvetica Neue', Helvetica, Arial, sans-serif";

	function roundRect( ctx, x, y, w, h, r ) {
		ctx.beginPath();
		ctx.moveTo( x + r, y );
		ctx.arcTo( x + w, y, x + w, y + h, r );
		ctx.arcTo( x + w, y + h, x, y + h, r );
		ctx.arcTo( x, y + h, x, y, r );
		ctx.arcTo( x, y, x + w, y, r );
		ctx.closePath();
	}

	/** The logo mark, drawn with the same geometry as the SVG. */
	function drawMark( ctx, x, y, size ) {
		var s = size / 64;
		ctx.save();
		ctx.translate( x, y );
		ctx.fillStyle = TEAL;
		roundRect( ctx, 0, 0, size, size, 15 * s );
		ctx.fill();
		ctx.fillStyle = '#FFFFFF';
		roundRect( ctx, 15 * s, 23.5 * s, 34 * s, 6.5 * s, 3.25 * s );
		ctx.fill();
		roundRect( ctx, 15 * s, 35.5 * s, 21 * s, 6.5 * s, 3.25 * s );
		ctx.fill();
		ctx.fillStyle = '#E9A23B';
		ctx.beginPath();
		ctx.arc( 44.5 * s, 38.75 * s, 4.75 * s, 0, Math.PI * 2 );
		ctx.fill();
		ctx.restore();
	}

	/** Shrinks the type until the text fits, rather than letting it run off. */
	function fitText( ctx, text, maxWidth, startSize, weight, family ) {
		var size = startSize;

		do {
			ctx.font = weight + ' ' + size + 'px ' + family;
			size -= 2;
		} while ( ctx.measureText( text ).width > maxWidth && size > 18 );

		return size + 2;
	}

	function truncate( ctx, text, maxWidth ) {
		if ( ctx.measureText( text ).width <= maxWidth ) {
			return text;
		}

		var out = text;
		while ( out.length > 1 && ctx.measureText( out + '…' ).width > maxWidth ) {
			out = out.slice( 0, -1 );
		}

		return out + '…';
	}

	/**
	 * The mark for the watermark band: a white tile with the bars knocked out
	 * in the band's own colour. An earlier version used destination-out
	 * compositing to punch the bars through, which erased the band underneath
	 * as well and rendered as a featureless white square.
	 */
	function drawMarkMono( ctx, x, y, size, tile, knockout ) {
		var s = size / 64;
		ctx.save();
		ctx.translate( x, y );
		ctx.fillStyle = tile;
		roundRect( ctx, 0, 0, size, size, 15 * s );
		ctx.fill();
		ctx.fillStyle = knockout;
		roundRect( ctx, 15 * s, 23.5 * s, 34 * s, 6.5 * s, 3.25 * s );
		ctx.fill();
		roundRect( ctx, 15 * s, 35.5 * s, 21 * s, 6.5 * s, 3.25 * s );
		ctx.fill();
		ctx.beginPath();
		ctx.arc( 44.5 * s, 38.75 * s, 4.75 * s, 0, Math.PI * 2 );
		ctx.fill();
		ctx.restore();
	}

	/**
	 * Two formats, because the platforms genuinely want different shapes.
	 * Landscape is the universal link-preview ratio that Twitter, LinkedIn,
	 * Facebook, Slack and WhatsApp all render. Square is for Instagram and
	 * anywhere else a feed crops to 1:1, and it exists as its own layout
	 * rather than as a crop of the landscape one, because cropping a
	 * left-aligned card slices the number in half.
	 */
	var FORMATS = {
		landscape: { w: 1200, h: 630, accent: 10, band: 88, mark: 44, gap: 80, value: 76, rowGap: 42 },
		square:    { w: 1080, h: 1080, accent: 12, band: 112, mark: 48, gap: 210, value: 104, rowGap: 58 }
	};

	function drawCard( result, heading, format ) {
		var spec = FORMATS[ format ] || FORMATS.square;
		var W = spec.w;
		var H = spec.h;
		var pad = 64;
		var scale = 2;

		var canvas = document.createElement( 'canvas' );
		canvas.width = W * scale;
		canvas.height = H * scale;

		var ctx = canvas.getContext( '2d' );
		ctx.scale( scale, scale );
		ctx.textBaseline = 'alphabetic';
		ctx.textAlign = 'left';

		ctx.fillStyle = PAPER;
		ctx.fillRect( 0, 0, W, H );

		ctx.fillStyle = TEAL;
		ctx.fillRect( 0, 0, W, spec.accent );

		var bandTop = H - spec.band;
		var markY = spec.accent + 40;
		var headerBaseline = markY + spec.mark - 10;

		drawMark( ctx, pad, markY, spec.mark );
		ctx.font = '700 30px ' + DISPLAY;
		ctx.fillStyle = INK;
		ctx.fillText( 'calculato', pad + spec.mark + 16, headerBaseline );
		var w = ctx.measureText( 'calculato' ).width;
		ctx.fillStyle = TEAL;
		ctx.fillText( 'rr', pad + spec.mark + 16 + w, headerBaseline );

		ctx.font = '600 22px ' + BODY;
		ctx.fillStyle = MUTED;
		ctx.textAlign = 'right';
		ctx.fillText( truncate( ctx, heading || '', W - pad * 2 - 300 ), W - pad, headerBaseline );
		ctx.textAlign = 'left';

		var labelY = headerBaseline + spec.gap;

		ctx.font = '600 26px ' + BODY;
		ctx.fillStyle = TEAL;
		ctx.fillText( truncate( ctx, result.label || 'Result', W - pad * 2 ), pad, labelY );

		var valueText = String( result.value === undefined || result.value === null ? '\u2014' : result.value );
		var valueSize = fitText( ctx, valueText, W - pad * 2, spec.value, '700', DISPLAY );
		ctx.font = '700 ' + valueSize + 'px ' + DISPLAY;
		ctx.fillStyle = INK;
		ctx.fillText( valueText, pad, labelY + valueSize + 8 );

		var y = labelY + valueSize + 56;

		ctx.strokeStyle = LINE;
		ctx.lineWidth = 1;
		ctx.beginPath();
		ctx.moveTo( pad, y );
		ctx.lineTo( W - pad, y );
		ctx.stroke();

		y += spec.rowGap + 2;

		/* Rows are capped by the space left above the band rather than by a
		   fixed count, so a long value can never push one underneath it. */
		( result.rows || [] ).forEach( function ( row ) {
			if ( y > bandTop - 30 ) {
				return;
			}

			ctx.font = '400 24px ' + BODY;
			ctx.fillStyle = MUTED;
			ctx.textAlign = 'left';
			ctx.fillText( truncate( ctx, row.label, W * 0.52 ), pad, y );

			ctx.font = '600 25px ' + DISPLAY;
			ctx.fillStyle = row.emphasis ? AMBER : INK;
			ctx.textAlign = 'right';
			ctx.fillText( truncate( ctx, String( row.value ), W * 0.35 ), W - pad, y );

			ctx.textAlign = 'left';
			y += spec.rowGap;
		} );

		/* The watermark band, centred so it survives any crop a feed applies. */
		ctx.fillStyle = TEAL;
		ctx.fillRect( 0, bandTop, W, spec.band );

		var markSize = 40;
		var domain = 'calculatorr.org';

		ctx.font = '700 32px ' + DISPLAY;
		var domainW = ctx.measureText( domain ).width;
		var groupW = markSize + 14 + domainW;
		var startX = ( W - groupW ) / 2;
		var bandMid = bandTop + spec.band / 2;

		drawMarkMono( ctx, startX, bandMid - markSize / 2, markSize, '#FFFFFF', TEAL );

		ctx.fillStyle = '#FFFFFF';
		ctx.textAlign = 'left';
		ctx.fillText( domain, startX + markSize + 14, bandMid + 11 );
		ctx.textAlign = 'left';

		return canvas;
	}

	function toBlob( canvas ) {
		return new Promise( function ( resolve ) {
			if ( canvas.toBlob ) {
				/* JPEG at 0.92 keeps the file small enough for a message
				   attachment while staying clean on flat colour and type. */
				canvas.toBlob( resolve, 'image/jpeg', 0.92 );
				return;
			}
			resolve( null );
		} );
	}

	window.CalculatorrShare = {
		drawCard: drawCard,
		toBlob: toBlob
	};
}() );

/*
 * The share panel.
 *
 * State travels in the URL fragment rather than the query string, deliberately.
 * A fragment never reaches the server, so it cannot collide with WordPress
 * query variables, and crawlers ignore it, which means a hundred thousand
 * shared permutations never become a hundred thousand near-duplicate URLs
 * competing with the page they came from. The cost is that the server cannot
 * read the state, so link previews show the calculator's own card rather than
 * the individual result.
 */
( function () {
	'use strict';

	var Share = window.CalculatorrShare;

	function shareUrl( root ) {
		var params = [];

		root.querySelectorAll( '[data-calcr-input]' ).forEach( function ( el ) {
			var key = el.getAttribute( 'data-calcr-input' );
			params.push( encodeURIComponent( key ) + '=' + encodeURIComponent( el.value ) );
		} );

		var base = window.location.origin + window.location.pathname;
		return params.length ? base + '#' + params.join( '&' ) : base;
	}

	function resultText( root ) {
		var result = root.__calcrResult || {};
		return ( result.label || 'Result' ) + ': ' + ( result.value || '' );
	}

	function filename( root ) {
		var shape = 'square' === root.__calcrFormat ? '-square' : '';
		return ( root.getAttribute( 'data-calcr-slug' ) || 'calculatorr' ) + shape + '.jpg';
	}

	function heading( root ) {
		var h1 = document.querySelector( 'h1' );
		return h1 ? h1.textContent.trim() : '';
	}

	function currentCanvas( root ) {
		return Share.drawCard( root.__calcrResult || {}, heading( root ), root.__calcrFormat || 'landscape' );
	}

	/**
	 * Waits for the web fonts before drawing, because a card rendered while
	 * Space Grotesk is still loading comes out in a fallback face and looks
	 * nothing like the site it claims to be from.
	 */
	function whenReady( callback ) {
		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( callback, callback );
			return;
		}

		callback();
	}

	function download( root ) {
		whenReady( function () {
		var canvas = currentCanvas( root );

		Share.toBlob( canvas ).then( function ( blob ) {
			if ( ! blob ) {
				return;
			}

			var url = URL.createObjectURL( blob );
			var link = document.createElement( 'a' );
			link.href = url;
			link.download = filename( root );
			document.body.appendChild( link );
			link.click();
			document.body.removeChild( link );
			/* Revoked on the next tick, because revoking synchronously can
			   cancel the download in some browsers before it has started. */
			window.setTimeout( function () { URL.revokeObjectURL( url ); }, 4000 );
		} );
		} );
	}

	function tweet( root ) {
		var text = resultText( root ) + ' — ' + heading( root );
		var url = 'https://twitter.com/intent/tweet'
			+ '?text=' + encodeURIComponent( text )
			+ '&url=' + encodeURIComponent( shareUrl( root ) );

		window.open( url, '_blank', 'noopener,noreferrer,width=560,height=460' );
	}

	function nativeShare( root, button ) {
		var text = resultText( root );
		var url = shareUrl( root );

		whenReady( function () {
		var canvas = currentCanvas( root );

		Share.toBlob( canvas ).then( function ( blob ) {
			var payload = { title: heading( root ), text: text, url: url };

			/* Sharing the image itself is what makes this worth doing, but not
			   every platform accepts files, so the capability is tested before
			   the file is attached rather than after the share fails. */
			if ( blob && window.File && navigator.canShare ) {
				var file = new File( [ blob ], filename( root ), { type: 'image/jpeg' } );

				if ( navigator.canShare( { files: [ file ] } ) ) {
					payload.files = [ file ];
				}
			}

			navigator.share( payload ).catch( function () {
				/* A cancelled share sheet rejects too, so there is nothing
				   here worth telling the person about. */
			} );
		} );
		} );
	}

	function flash( button, message ) {
		var label = button.querySelector( '[data-calcr-label]' );

		if ( ! label ) {
			return;
		}

		var original = label.getAttribute( 'data-original' ) || label.textContent;
		label.setAttribute( 'data-original', original );
		label.textContent = message;
		window.clearTimeout( button.__flash );
		button.__flash = window.setTimeout( function () {
			label.textContent = original;
		}, 2000 );
	}

	function copyLink( root, button ) {
		var url = shareUrl( root );

		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( url ).then(
				function () { flash( button, 'Link copied' ); },
				function () { window.prompt( 'Copy this link', url ); }
			);
			return;
		}

		window.prompt( 'Copy this link', url );
	}

	function renderPreview( root, panel ) {
		var holder = panel.querySelector( '[data-calcr-preview]' );

		if ( ! holder ) {
			return;
		}

		whenReady( function () {
		var canvas = currentCanvas( root );
		holder.innerHTML = '';
		canvas.style.width = '100%';
		canvas.style.height = 'auto';
		canvas.setAttribute( 'role', 'img' );
		canvas.setAttribute( 'aria-label', 'Shareable image of this result' );
		holder.appendChild( canvas );
		} );
	}

	function bind( root ) {
		var toggle = root.querySelector( '[data-calcr-share-toggle]' );
		var panel = root.querySelector( '[data-calcr-share-panel]' );

		if ( ! toggle || ! panel ) {
			return;
		}

		/* The native sheet is the best experience where it exists, so it is
		   offered first and simply removed where it does not. */
		var nativeButton = panel.querySelector( '[data-calcr-share-native]' );

		if ( nativeButton && ! navigator.share ) {
			nativeButton.remove();
		}

		var open = function ( state ) {
			panel.hidden = ! state;
			toggle.setAttribute( 'aria-expanded', state ? 'true' : 'false' );

			if ( state ) {
				renderPreview( root, panel );
			}
		};

		toggle.addEventListener( 'click', function () {
			open( panel.hidden );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! panel.hidden && ! panel.contains( event.target ) && ! toggle.contains( event.target ) ) {
				open( false );
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && ! panel.hidden ) {
				open( false );
				toggle.focus();
			}
		} );

		panel.addEventListener( 'click', function ( event ) {
			var button = event.target.closest( 'button' );

			if ( ! button ) {
				return;
			}

			if ( button.hasAttribute( 'data-calcr-format' ) ) {
				root.__calcrFormat = button.getAttribute( 'data-calcr-format' );

				panel.querySelectorAll( '[data-calcr-format]' ).forEach( function ( sibling ) {
					var active = sibling === button;
					sibling.classList.toggle( 'is-active', active );
					sibling.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
				} );

				renderPreview( root, panel );
				return;
			}

			if ( button.hasAttribute( 'data-calcr-share-native' ) ) { nativeShare( root, button ); }
			if ( button.hasAttribute( 'data-calcr-share-tweet' ) ) { tweet( root ); }
			if ( button.hasAttribute( 'data-calcr-share-link' ) ) { copyLink( root, button ); }
			if ( button.hasAttribute( 'data-calcr-share-download' ) ) { download( root ); flash( button, 'Saved' ); }
		} );
	}

	window.CalculatorrShare.bind = bind;
	window.CalculatorrShare.url = shareUrl;
}() );
