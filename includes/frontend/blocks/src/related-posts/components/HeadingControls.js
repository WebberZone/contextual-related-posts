import { __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';

export const HeadingControls = ({
	heading,
	title,
	onChangeTitle,
	toggleHeading,
}) => (
	<>
		<PanelRow>
			<ToggleControl
				label={__('Show heading', 'contextual-related-posts')}
				help={
					heading
						? __('Heading displayed', 'contextual-related-posts')
						: __('No Heading displayed', 'contextual-related-posts')
				}
				checked={heading}
				onChange={toggleHeading}
			/>
		</PanelRow>
		{heading && (
			<PanelRow>
				<TextControl
					label={__('Heading of posts', 'contextual-related-posts')}
					value={title}
					onChange={onChangeTitle}
					help={__(
						'Displayed before the list of the posts as a master heading. HTML allowed.',
						'contextual-related-posts'
					)}
				/>
			</PanelRow>
		)}
	</>
);
