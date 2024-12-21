import { __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';

export const PostDisplayControls = ({
	show_excerpt,
	show_author,
	show_date,
	toggleShowExcerpt,
	toggleShowAuthor,
	toggleShowDate,
}) => (
	<>
		<PanelRow>
			<ToggleControl
				label={__('Show excerpt', 'contextual-related-posts')}
				help={
					show_excerpt
						? __('Excerpt displayed', 'contextual-related-posts')
						: __('No excerpt', 'contextual-related-posts')
				}
				checked={show_excerpt}
				onChange={toggleShowExcerpt}
			/>
		</PanelRow>
		<PanelRow>
			<ToggleControl
				label={__('Show author', 'contextual-related-posts')}
				help={
					show_author
						? __(
								'"by Author Name" displayed',
								'contextual-related-posts'
							)
						: __('No author displayed', 'contextual-related-posts')
				}
				checked={show_author}
				onChange={toggleShowAuthor}
			/>
		</PanelRow>
		<PanelRow>
			<ToggleControl
				label={__('Show date', 'contextual-related-posts')}
				help={
					show_date
						? __(
								'Date of post displayed',
								'contextual-related-posts'
							)
						: __(
								'Date of post not displayed',
								'contextual-related-posts'
							)
				}
				checked={show_date}
				onChange={toggleShowDate}
			/>
		</PanelRow>
	</>
);
