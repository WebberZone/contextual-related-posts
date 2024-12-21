import { registerBlockType } from '@wordpress/blocks';

import { postIcon } from './components/icons';
import Edit from './edit';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	...metadata,
	icon: postIcon,
	edit: Edit,
});
