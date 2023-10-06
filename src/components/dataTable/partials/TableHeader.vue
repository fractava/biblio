<template>
	<tr>
		<th class="sticky">
			<div class="cell-wrapper">
				<NcCheckboxRadioSwitch :checked="allRowsAreSelected" @update:checked="value => $emit('select-all-rows', value)" />
			</div>
		</th>
		<th v-for="col in columns" :key="col.id">
			<div class="cell-wrapper">
				<div class="cell-options-wrapper">
					<div class="cell">
						<div class="clickable" @click="updateOpenState(col.id)">
							{{ col.name }}
						</div>
						<TableHeaderColumnOptions :column="col"
							:open-state.sync="openedColumnHeaderMenus[col.id]"
							:current-sort="currentSort"
							:current-sort-reverse="currentSortReverse"
							:current-filters="currentFilters"
							@update:currentSort="(newSort) => $emit('update:currentSort', newSort)"
							@update:currentSortReverse="(newSortReverse) => $emit('update:currentSortReverse', newSortReverse)"
							@update:currentFilters="(newFilters) => $emit('update:currentFilters', newFilters)" />
					</div>
					<div v-if="col.canFilter && getFilterForColumn(col)" class="filter-wrapper">
						<FilterLabel :id="col.sortIdentifier"
							:label="getFilterLabel(col, getFilterForColumn(col).operator)"
							:operand="getFilterForColumn(col).operand"
							@delete-filter="id => deleteFilter(id)" />
					</div>
				</div>
			</div>
		</th>
	</tr>
</template>

<script>
import { NcCheckboxRadioSwitch } from "@nextcloud/vue";
import TableHeaderColumnOptions from "./TableHeaderColumnOptions.vue";
import FilterLabel from "./FilterLabel.vue";

export default {

	components: {
		FilterLabel,
		NcCheckboxRadioSwitch,
		TableHeaderColumnOptions,
	},

	props: {
		columns: {
			type: Array,
			default: () => [],
		},
		rows: {
			type: Array,
			default: () => [],
		},
		selectedRows: {
			type: Array,
			default: () => [],
		},
		currentSort: {
			type: String,
			default: "",
		},
		currentSortReverse: {
			type: Boolean,
			default: false,
		},
		currentFilters: {
			type: Object,
			default: () => ({}),
		},
	},

	data() {
		return {
			openedColumnHeaderMenus: {},
		};
	},

	computed: {
		allRowsAreSelected() {
			if (Array.isArray(this.rows) && Array.isArray(this.selectedRows) && this.rows.length !== 0) {
				return this.rows.length === this.selectedRows.length;
			} else {
				return false;
			}
		},
	},

	methods: {
		updateOpenState(columnId) {
			this.openedColumnHeaderMenus[columnId] = !this.openedColumnHeaderMenus[columnId];
			this.openedColumnHeaderMenus = Object.assign({}, this.openedColumnHeaderMenus);
		},
		getFilterForColumn(column) {
			return this.currentFilters[column.sortIdentifier];
		},
		getFilterLabel(column, operatorId) {
			return column.filterOperators.find((operator) => (operator.id === operatorId))?.label;
		},
		deleteFilter(sortIdentifier) {
			const { [sortIdentifier]: _, ...newFilters } = this.currentFilters;
			this.$emit("update:currentFilters", newFilters);
		},
	},
};
</script>
<style lang="scss" scoped>

.cell {
	display: inline-flex;
	align-items: center;
}

.cell span {
	padding-left: 12px;

}

.filter-wrapper {
	margin-top: calc(var(--default-grid-baseline) * -1);
	margin-bottom: calc(var(--default-grid-baseline) * 2);
	display: flex;
	flex-wrap: wrap;
	gap: 0 calc(var(--default-grid-baseline) * 2);
}

:deep(.checkbox-radio-switch__icon) {
	margin: 0;
}

.clickable {
	cursor: pointer;
}

.cell-wrapper {
	display: flex;
	justify-content: space-between;
}

.cell-options-wrapper {
	display: flex;
	flex-direction: column;
	width: 100%;
}

</style>
