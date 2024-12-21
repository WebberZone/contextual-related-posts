import { __ } from '@wordpress/i18n';
import { PanelRow, TextControl } from '@wordpress/components';

export const PostLimitControls = ({
	limit,
	offset,
	onChangeLimit,
	onChangeOffset,
}) => (
	<>
		<PanelRow>
			<TextControl
				label={__('Number of posts', 'contextual-related-posts')}
				value={limit}
				onChange={onChangeLimit}
				help={__(
					'Maximum number of posts to display',
					'contextual-related-posts'
				)}
			/>
		</PanelRow>
		<PanelRow>
			<TextControl
				label={__('Offset', 'contextual-related-posts')}
				value={offset}
				onChange={onChangeOffset}
				help={__(
					'Number of posts to skip from the top',
					'contextual-related-posts'
				)}
			/>
		</PanelRow>
	</>
);
