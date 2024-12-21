import { __ } from '@wordpress/i18n';
import { PanelRow, SelectControl } from '@wordpress/components';

export const ThumbnailControls = ({ post_thumb_op, onChangeThumbnail }) => (
	<PanelRow>
		<SelectControl
			label={__('Thumbnail option', 'contextual-related-posts')}
			value={post_thumb_op}
			onChange={onChangeThumbnail}
			help={__(
				'Location of the post thumbnail',
				'contextual-related-posts'
			)}
			options={[
				{
					value: 'inline',
					label: __('Before title', 'contextual-related-posts'),
				},
				{
					value: 'after',
					label: __('After title', 'contextual-related-posts'),
				},
				{
					value: 'thumbs_only',
					label: __('Only thumbnail', 'contextual-related-posts'),
				},
				{
					value: 'text_only',
					label: __('Only text', 'contextual-related-posts'),
				},
			]}
		/>
	</PanelRow>
);
