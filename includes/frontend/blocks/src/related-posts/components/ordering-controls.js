import { __ } from '@wordpress/i18n';
import { PanelRow, RadioControl, ToggleControl } from '@wordpress/components';

export const OrderingControls = ({
	ordering,
	random_order,
	onChangeOrdering,
	toggleRandomOrder,
}) => (
	<>
		<PanelRow>
			<RadioControl
				label={__('Order posts', 'contextual-related-posts')}
				selected={ordering}
				onChange={onChangeOrdering}
				help={__(
					'This option directly edits the query',
					'contextual-related-posts'
				)}
				options={[
					{
						value: 'relevance',
						label: __('By relevance', 'contextual-related-posts'),
					},
					{
						value: 'random',
						label: __('Randomly', 'contextual-related-posts'),
					},
					{
						value: 'date',
						label: __('By date', 'contextual-related-posts'),
					},
				]}
			/>
		</PanelRow>
		<PanelRow>
			<ToggleControl
				label={__('Randomize posts', 'contextual-related-posts')}
				help={
					random_order
						? __(
								'Posts are shuffled on each load',
								'contextual-related-posts'
							)
						: __(
								'Posts displayed based on above setting',
								'contextual-related-posts'
							)
				}
				checked={random_order}
				onChange={toggleRandomOrder}
			/>
		</PanelRow>
	</>
);
