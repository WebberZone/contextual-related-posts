const { execSync } = require( 'child_process' );
const { readdirSync, statSync, readFileSync, writeFileSync, existsSync, unlinkSync } = require( 'fs' );
const { join, dirname, resolve, extname } = require( 'path' );
const { mkdirSync } = require( 'fs' );

// Parse command line arguments.
const args = process.argv.slice( 2 );
const specificFiles = args.filter( arg => ! arg.startsWith( '--' ) );
const processSpecificFiles = specificFiles.length > 0;

/**
 * Configuration for asset processing.
 */
const config = {
	// Directories to exclude from processing.
	excludeDirs: [
		'node_modules',
		'vendor',
		'freemius',
		'build',
		'.git',
		'includes/blocks',
		'includes/frontend/blocks',
		'includes/pro/blocks',
	],
	
	// File patterns to exclude from discovery.
	// Note: We exclude -rtl.css but not .min files to allow re-minification.
	excludePatterns: [
		/-rtl\.css$/,
		/^build-assets\.js$/,
	],

	// CSS files to combine - order matters!
	// Example: 'path/to/output.css': ['path/to/source1.css', 'path/to/source2.css']
	combineCss: {},

	// JS files to combine - order matters!
	// Example: 'path/to/output.js': ['path/to/source1.js', 'path/to/source2.js']
	combineJs: {},

	// If true, minify source files before combining (results in smaller bundles).
	minifyBeforeCombine: false,

	// If true, keep individual .min versions of combined output files.
	createMinifiedCombinedFiles: true,
};

// Track errors for final exit code.
let errorCount = 0;

// Paths to local binaries.
const binaries = {
	cleancss: './node_modules/.bin/cleancss',
	terser: './node_modules/.bin/terser',
	rtlcss: './node_modules/.bin/rtlcss',
	wpscripts: './node_modules/.bin/wp-scripts',
};

/**
 * Ensure directory exists.
 *
 * @param {string} filePath File path to check.
 */
function ensureDirectoryExists( filePath ) {
	const dir = dirname( filePath );
	if ( ! existsSync( dir ) ) {
		mkdirSync( dir, { recursive: true } );
	}
}

/**
 * Check if a path should be excluded.
 *
 * @param {string} filePath File path to check.
 * @return {boolean} True if should be excluded.
 */
function shouldExclude( filePath ) {
	// Check directory exclusions.
	for ( const excludeDir of config.excludeDirs ) {
		if ( filePath.includes( `/${excludeDir}/` ) || filePath.startsWith( `${excludeDir}/` ) ) {
			return true;
		}
	}

	// Check pattern exclusions.
	for ( const pattern of config.excludePatterns ) {
		if ( pattern.test( filePath ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Recursively find all files with a specific extension.
 *
 * @param {string} dir       Directory to search.
 * @param {string} extension File extension to match.
 * @param {Array}  fileList  Accumulated file list.
 * @return {Array} List of file paths.
 */
function findFiles( dir, extension, fileList = [] ) {
	if ( ! existsSync( dir ) ) {
		return fileList;
	}

	const files = readdirSync( dir );

	files.forEach( ( file ) => {
		const filePath = join( dir, file );

		if ( shouldExclude( filePath ) ) {
			return;
		}

		const stat = statSync( filePath );

		if ( stat.isDirectory() ) {
			findFiles( filePath, extension, fileList );
		} else if ( file.endsWith( extension ) && ! shouldExclude( filePath ) ) {
			fileList.push( filePath );
		}
	} );

	return fileList;
}

/**
 * Minify a CSS file.
 *
 * @param {string} inputFile  Input file path.
 * @param {string} outputFile Output file path.
 * @return {boolean} True if successful.
 */
function minifyCss( inputFile, outputFile ) {
	try {
		ensureDirectoryExists( outputFile );
		execSync( `${binaries.cleancss} -o ${outputFile} ${inputFile}`, { stdio: 'pipe' } );
		return true;
	} catch ( error ) {
		console.error( `  ✗ Error minifying ${inputFile}:`, error.message );
		errorCount++;
		return false;
	}
}

/**
 * Minify a JS file.
 *
 * @param {string} inputFile  Input file path.
 * @param {string} outputFile Output file path.
 * @return {boolean} True if successful.
 */
function minifyJs( inputFile, outputFile ) {
	try {
		ensureDirectoryExists( outputFile );
		execSync( `${binaries.terser} ${inputFile} -o ${outputFile} -c -m`, { stdio: 'pipe' } );
		return true;
	} catch ( error ) {
		console.error( `  ✗ Error minifying ${inputFile}:`, error.message );
		errorCount++;
		return false;
	}
}

/**
 * Generate RTL version of CSS file.
 *
 * @param {string} inputFile  Input file path.
 * @param {string} outputFile Output file path.
 * @return {boolean} True if successful.
 */
function generateRtl( inputFile, outputFile ) {
	try {
		ensureDirectoryExists( outputFile );
		execSync( `${binaries.rtlcss} ${inputFile} ${outputFile}`, { stdio: 'pipe' } );
		return true;
	} catch ( error ) {
		console.error( `  ✗ Error creating RTL for ${inputFile}:`, error.message );
		errorCount++;
		return false;
	}
}

/**
 * Format a CSS file with wp-scripts (uses WordPress Prettier config).
 *
 * @param {string} file File path to format.
 * @return {boolean} True if successful.
 */
function formatCss( file ) {
	try {
		execSync( `${binaries.wpscripts} format ${file}`, { stdio: 'pipe' } );
		return true;
	} catch ( error ) {
		console.error( `  ✗ Error formatting ${file}:`, error.message );
		errorCount++;
		return false;
	}
}

/**
 * Combine multiple files into one.
 *
 * @param {string}  outputFile Output file path.
 * @param {Array}   inputFiles Array of input file paths.
 * @param {boolean} isJs       Whether files are JavaScript.
 */
function combineFiles( outputFile, inputFiles, isJs = false ) {
	console.log( `\nCombining files into: ${outputFile}` );
	
	let combinedContent = '';
	const tempFiles = [];
	
	inputFiles.forEach( ( file ) => {
		try {
			let content;

			// Minify before combining if configured.
			if ( config.minifyBeforeCombine ) {
				const tempMinFile = file.replace( /\.(css|js)$/, '.temp.min.$1' );
				tempFiles.push( tempMinFile );

				if ( isJs ) {
					if ( ! minifyJs( file, tempMinFile ) ) {
						return;
					}
				} else {
					if ( ! minifyCss( file, tempMinFile ) ) {
						return;
					}
				}

				content = readFileSync( tempMinFile, 'utf8' );
			} else {
				content = readFileSync( file, 'utf8' );
			}

			combinedContent += `\n/* Source: ${file} */\n`;
			combinedContent += content;
			combinedContent += '\n';
			console.log( `  ✓ Added: ${file}` );
		} catch ( error ) {
			console.error( `  ✗ Error reading ${file}:`, error.message );
			errorCount++;
		}
	} );

	try {
		ensureDirectoryExists( outputFile );
		writeFileSync( outputFile, combinedContent );
		console.log( `  ✓ Created: ${outputFile}` );

		// Clean up temp files (cross-platform).
		tempFiles.forEach( ( tempFile ) => {
			try {
				if ( existsSync( tempFile ) ) {
					unlinkSync( tempFile );
				}
			} catch ( error ) {
				// Silently ignore cleanup errors.
			}
		} );
	} catch ( error ) {
		console.error( `  ✗ Error writing ${outputFile}:`, error.message );
		errorCount++;
	}
}

// Display usage information if specific files are being processed.
if ( processSpecificFiles ) {
	console.log( '==================================' );
	console.log( 'Processing specific files:' );
	specificFiles.forEach( file => console.log( `  - ${file}` ) );
	console.log( '==================================' );
} else {
	console.log( '==================================' );
	console.log( 'Processing all assets in project' );
	console.log( 'Tip: Pass specific files to process only those:' );
	console.log( '  node build-assets.js path/to/file.js path/to/file.css' );
	console.log( '==================================' );
}

// Step 1: Combine CSS files.
console.log( '\n=== Combining CSS Files ===' );
Object.entries( config.combineCss ).forEach( ( [output, inputs] ) => {
	combineFiles( output, inputs, false );
} );

// Step 2: Combine JS files.
console.log( '\n=== Combining JS Files ===' );
Object.entries( config.combineJs ).forEach( ( [output, inputs] ) => {
	combineFiles( output, inputs, true );
} );

// Step 3: Build exclusion lists.
const combinedSourceFiles = [
	...Object.values( config.combineCss ).flat(),
	...Object.values( config.combineJs ).flat(),
];

const combinedOutputFiles = [
	...Object.keys( config.combineCss ),
	...Object.keys( config.combineJs ),
];

// Step 4: Find all CSS and JS files.
let allCssFiles = [];
let allJsFiles = [];

if ( processSpecificFiles ) {
	console.log( '\n=== Processing Specific Files ===' );
	specificFiles.forEach( file => {
		const resolvedPath = resolve( file );
		const ext = extname( file );
		if ( existsSync( resolvedPath ) ) {
			if ( ext === '.css' ) {
				allCssFiles.push( file );
				console.log( `  ✓ Found CSS: ${file}` );
			} else if ( ext === '.js' ) {
				allJsFiles.push( file );
				console.log( `  ✓ Found JS: ${file}` );
			} else {
				console.log( `  ✗ Skipped (not CSS/JS): ${file}` );
			}
		} else {
			console.log( `  ✗ File not found: ${file}` );
		}
	} );
} else {
	allCssFiles = findFiles( '.', '.css' );
	allJsFiles = findFiles( '.', '.js' );
}

// Filter out source files used in combinations.
const cssFilesToMinify = allCssFiles.filter( ( file ) => {
	if ( combinedSourceFiles.includes( file ) ) {
		return false;
	}
	if ( file.endsWith( '.min.css' ) ) {
		return false;
	}
	if ( ! config.createMinifiedCombinedFiles && combinedOutputFiles.includes( file ) ) {
		return false;
	}
	return true;
} );

const jsFilesToMinify = allJsFiles.filter( ( file ) => {
	if ( combinedSourceFiles.includes( file ) ) {
		return false;
	}
	if ( file.endsWith( '.min.js' ) ) {
		return false;
	}
	if ( ! config.createMinifiedCombinedFiles && combinedOutputFiles.includes( file ) ) {
		return false;
	}
	return true;
} );

console.log( `\n=== Processing Assets ===` );
console.log( `Found ${cssFilesToMinify.length} CSS files and ${jsFilesToMinify.length} JS files to minify.\n` );

// Step 5: Minify CSS files.
console.log( '=== Minifying CSS ===' );
cssFilesToMinify.forEach( ( file ) => {
	const minFile = file.replace( '.css', '.min.css' );
	console.log( `  ${file} → ${minFile}` );
	minifyCss( file, minFile );
} );

// Step 6: Minify JS files.
console.log( '\n=== Minifying JS ===' );
jsFilesToMinify.forEach( ( file ) => {
	const minFile = file.replace( '.js', '.min.js' );
	console.log( `  ${file} → ${minFile}` );
	minifyJs( file, minFile );
} );

// Step 7: Generate RTL versions for CSS files.
console.log( '\n=== Generating RTL CSS ===' );
const cssFilesForRtl = allCssFiles.filter( ( file ) => ! file.includes( '-rtl.' ) && ! file.endsWith( '-rtl.css' ) );

if ( processSpecificFiles && cssFilesForRtl.length === 0 ) {
	console.log( '  No CSS files to process for RTL.' );
}

const rtlFilesGenerated = [];
cssFilesForRtl.forEach( ( file ) => {
	// Handle .min.css files correctly: name.min.css → name-rtl.min.css
	const rtlFile = file.endsWith( '.min.css' )
		? file.replace( '.min.css', '-rtl.min.css' )
		: file.replace( '.css', '-rtl.css' );
	console.log( `  ${file} → ${rtlFile}` );
	if ( generateRtl( file, rtlFile ) ) {
		// Only format non-minified RTL files.
		if ( ! rtlFile.endsWith( '.min.css' ) ) {
			rtlFilesGenerated.push( rtlFile );
		}
	}
} );

// Step 8: Format RTL CSS files for better readability.
if ( rtlFilesGenerated.length > 0 ) {
	console.log( '\n=== Formatting RTL CSS ===' );
	rtlFilesGenerated.forEach( ( file ) => {
		console.log( `  Formatting: ${file}` );
		formatCss( file );
	} );
}

// Final summary.
console.log( '\n==================================' );
if ( errorCount > 0 ) {
	console.error( `✗ Completed with ${errorCount} error(s).` );
	process.exit( 1 );
} else {
	console.log( '✓ All assets processed successfully!' );
	process.exit( 0 );
}