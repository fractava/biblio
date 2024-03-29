<template>
	<div class="container">
		<table :class="{ hasMaximizedColumn }">
			<thead>
				<TableHeader :columns="columns"
					:selected-rows="localSelectedRows"
					:rows="rows"
					:current-sort="currentSort"
					:current-sort-reverse="currentSortReverse"
					:current-filters="currentFilters"
					@update:currentSort="(newSort) => $emit('update:currentSort', newSort)"
					@update:currentSortReverse="(newSortReverse) => $emit('update:currentSortReverse', newSortReverse)"
					@update:currentFilters="(newFilters) => $emit('update:currentFilters', newFilters)"
					@create-row="$emit('create-row')"
					@create-column="$emit('create-column')"
					@edit-column="col => $emit('edit-column', col)"
					@download-csv="data => $emit('download-csv', data)"
					@select-all-rows="selectAllRows">
					<template #actions>
						<slot name="actions" />
					</template>
				</TableHeader>
			</thead>
			<tbody>
				<TableRow v-for="(row, index) in rows"
					:key="index"
					:row="row"
					:columns="columns"
					:selected="isRowSelected(row.id)"
					@update-row-selection="updateRowSelection"
					@click-row="(rowId, columnId)=> $emit('click-row', rowId, columnId)" />
			</tbody>
		</table>
	</div>
</template>

<script>
import TableHeader from "../partials/TableHeader.vue";
import TableRow from "../partials/TableRow.vue";
import { subscribe, unsubscribe } from "@nextcloud/event-bus";

export default {
	name: "CustomTable",

	components: {
		TableRow,
		TableHeader,
	},

	props: {
		rows: {
			type: Array,
			default: () => [],
		},
		columns: {
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

	computed: {
		localSelectedRows: {
			get() {
				return this.selectedRows;
			},
			set(selectedRows) {
				this.$emit("update:selectedRows", selectedRows);
			},
		},
		hasMaximizedColumn() {
			for (const column of this.columns) {
				if (column.maximizeWidth) {
					return true;
				}
			}
			return false;
		},
	},

	mounted() {
		subscribe("biblio:selected-rows:deselect", this.deselectAllRows);
	},
	beforeDestroy() {
		unsubscribe("biblio:selected-rows:deselect", this.deselectAllRows);
	},

	methods: {
		getFiltersForColumn(column) {
			if (this.viewSetting?.filter?.length > 0) {
				const columnFilter = this.viewSetting.filter.filter(item => item.columnId === column.id);
				if (columnFilter.length > 0) {
					return columnFilter;
				}
			}
			return null;
		},
		deselectAllRows() {
			this.localSelectedRows = [];
		},
		selectAllRows(value) {
			const newLocalSelectedRows = [];
			if (value) {
				this.rows.forEach(item => { newLocalSelectedRows.push(item.id); });
			}
			this.localSelectedRows = newLocalSelectedRows;
		},
		isRowSelected(id) {
			return this.selectedRows.includes(id);
		},
		updateRowSelection(values) {
			const id = values.rowId;
			const v = values.value;

			if (this.localSelectedRows.includes(id) && !v) {
				const index = this.localSelectedRows.indexOf(id);
				if (index > -1) {
					this.localSelectedRows.splice(index, 1);
				}
			}
			if (!this.localSelectedRows.includes(id) && v) {
				this.localSelectedRows.push(values.rowId);
			}
		},
	},
};
</script>

<style lang="scss" scoped>

.container {
	//margin: auto;
	overflow-x: auto;
}

:deep(table) {
	position: relative;
	border-collapse: collapse;
	border-spacing: 0;
	/*table-layout: fixed;*/
	max-width: 100%;
	width: 100%;
	border: none;

	* {
		border: none;
	}
	// white-space: nowrap;

	td {
		text-overflow: ellipsis;
		max-width: 100px;
		word-break: break-all;
		overflow: hidden;
	}

	&.hasMaximizedColumn :where(th, td):not(:first-child):not(.max) {
		width: 0;
		white-space: nowrap;
	}

	td, th {
		padding-right: 8px;
	}

	td .showOnHover, th .showOnHover {
		opacity: 0;
	}

	td:hover .showOnHover, th:hover .showOnHover, .showOnHover:focus-within {
		opacity: 1;
	}

	td:not(:first-child), th:not(:first-child) {
		padding-right: 8px;
		padding-left: 8px;
	}

	tr {
		height: 51px;
		background-color: var(--color-main-background);
	}

	thead tr {
		// text-align: left;

		th {
			vertical-align: middle;
			color: var(--color-text-maxcontrast);

			// sticky head
			// position: -webkit-sticky;
			// position: sticky;
			// top: 80px;
			box-shadow: inset 0 -1px 0 var(--color-border); // use box-shadow instead of border to be compatible with sticky heads
			background-color: var(--color-main-background-translucent);
			z-index: 5;

			// always fit to title
			// min-width: max-content;
		}

	}

	tbody {

		td {
			text-align: left;
			vertical-align: middle;
			border-bottom: 1px solid var(--color-border);
		}

		tr:active, tr:hover, tr:focus, tr:hover .editor-wrapper .editor {
			background-color: var(--color-background-dark);
		}

		tr:focus-within > td:last-child {
			opacity: 1;
		}
	}

	tr>th.sticky:first-child,tr>td.sticky:first-child {
		position: sticky;
		left: 0;
		padding-left: calc(var(--default-grid-baseline) * 4);
		padding-right: calc(var(--default-grid-baseline) * 4);
		width: 60px;
		background-color: inherit;
		z-index: 5;
	}

	tr>th.sticky:last-child,tr>td.sticky:last-child {
		position: sticky;
		right: 0;
		width: 55px;
		background-color: inherit;
		padding-right: 16px;
	}

	tr>td.sticky:last-child {
		// visibility: hidden;
		opacity: 0;
	}

	tr:hover>td:last-child {
		// visibility: visible;
		opacity: 1;
	}

}

</style>
