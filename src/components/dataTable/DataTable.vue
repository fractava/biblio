<template>
	<div class="DataTable">
		<div class="options row" style="padding-right: calc(var(--default-grid-baseline) * 2);">
			<Options :rows="rows"
				:selected-rows="localSelectedRows"
				:can-create-rows="canCreateRows"
				:create-row-label="createRowLabel"
				:can-search="canSearch"
				:can-delete-rows="canDeleteRows"
				:show-options="columns.length !== 0"
				:row-limit-filter="rowLimitFilter"
				:page="page"
				:max-page="maxPage"
				@create-row="$emit('create-row')"
				@download-csv="data => downloadCsv(data, columns, downloadTitle)"
				@set-search-string="str => $emit('set-search-string', str)"
				@update:rowLimitFilter="limit => $emit('update:rowLimitFilter', limit)"
				@delete-selected-rows="rowIds => $emit('delete-selected-rows', rowIds)"
				@update:page="newPage => $emit('update:page', newPage)" />
		</div>
		<div class="custom-table row">
			<CustomTable :columns="columns"
				:rows="rows"
				:selected-rows="localSelectedRows"
				:current-sort="currentSort"
				:current-sort-reverse="currentSortReverse"
				:current-filters="currentFilters"
				@update:currentSort="(newSort) => $emit('update:currentSort', newSort)"
				@update:currentSortReverse="(newSortReverse) => $emit('update:currentSortReverse', newSortReverse)"
				@update:currentFilters="(newFilters) => $emit('update:currentFilters', newFilters)"
				@create-row="$emit('create-row')"
				@click-row="(rowId, columnId)=> $emit('click-row', rowId, columnId)"
				@update:selectedRows="rowIds => localSelectedRows = rowIds"
				@download-csv="data => downloadCsv(data, columns, table)">
				<template #actions>
					<slot name="actions" />
				</template>
			</CustomTable>
			<NcEmptyContent v-if="rows.length === 0 && canCreateRows"
				:name="createRowLabel"
				:description="createRowDescription">
				<template #icon>
					<Plus :size="25" />
				</template>
				<template #action>
					<NcButton :aria-label="createRowLabel" type="primary" @click="$emit('create-row')">
						<template #icon>
							<Plus :size="25" />
						</template>
						{{ createRowLabel }}
					</NcButton>
				</template>
			</NcEmptyContent>
			<NcEmptyContent v-if="rows.length === 0 && !canCreateRows"
				:name="t('biblio', 'No Rows')">
				<template #icon>
					<GridOff :size="25" />
				</template>
			</NcEmptyContent>
			<slot v-if="rows.length > 0" name="footer" />
		</div>
	</div>
</template>

<script>
import NcEmptyContent from "@nextcloud/vue/dist/Components/NcEmptyContent.js";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import { subscribe, unsubscribe } from "@nextcloud/event-bus";

import Options from "./sections/Options.vue";
import CustomTable from "./sections/CustomTable.vue";
import exportTableMixin from "./mixins/exportTableMixin.js";

import Plus from "vue-material-design-icons/Plus.vue";
import GridOff from "vue-material-design-icons/GridOff.vue";

export default {
	name: "DataTable",

	components: {
		CustomTable,
		Options,
		NcButton,
		NcEmptyContent,
		Plus,
		GridOff,
	},

	mixins: [exportTableMixin],

	props: {
		rows: {
			type: Array,
			default: () => [],
		},
		columns: {
			type: Array,
			default: () => [],
		},
		page: {
			type: Number,
			default: 1,
		},
		maxPage: {
			type: Number,
			default: 1,
		},
		downloadTitle: {
			type: String,
			default: t("biblio", "Download"),
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
		rowLimitFilter: {
			type: Number,
			default: 100,
		},
		selectedRows: {
			type: Array,
			default: null,
		},
		canCreateRows: {
			type: Boolean,
			default: true,
		},
		createRowLabel: {
			type: String,
			default: t("biblio", "Create Row"),
		},
		createRowDescription: {
			type: String,
			default: t("biblio", "There are currently no rows in this table"),
		},
		canSearch: {
			type: Boolean,
			default: true,
		},
		canDeleteRows: {
			type: Boolean,
			default: true,
		},
	},
	data() {
		return {
			localSelectedRows: [],
		};
	},
	computed: {
	},
	watch: {
		localSelectedRows() {
			this.$emit("update:selectedRows", this.localSelectedRows);
		},
		rows() {
			// ensure all selected rows are actually still shown in DataTable
			const newLocalSelectedRows = [];

			for (const selectedRow of this.localSelectedRows) {
				if (this.rows.findIndex((row) => (row.id === selectedRow)) !== -1) {
					newLocalSelectedRows.push(selectedRow);
				}
			}

			this.localSelectedRows = newLocalSelectedRows;
		},
	},
	mounted() {
		subscribe("biblio:selected-rows:deselect", this.deselectRows);
	},
	beforeDestroy() {
		unsubscribe("biblio:selected-rows:deselect", this.deselectRows);
	},
	methods: {
		deselectRows() {
			this.localSelectedRows = [];
		},
	},
};
</script>

<style scoped lang="scss">

.options.row {
	position: sticky;
	top: 0;
	left: 0;
	z-index: 15;
	background-color: var(--color-main-background-translucent);
	padding-top: 4px; // fix to show buttons completely
	padding-bottom: 4px; // to make it nice with the padding-top
}
</style>
