<template>
	<div class="options">
		<div v-if="showOptions" class="optionsRow optionsRow1 fix-col-4">
			<NcButton v-if="!isSmallMobile && canCreateRows"
				class="addRowButton"
				:aria-label="createRowLabel"
				:close-after-click="true"
				type="secondary"
				@click="$emit('create-row')">
				{{ createRowLabel }}
				<template #icon>
					<Plus :size="25" />
				</template>
			</NcButton>
			<NcButton v-if="isSmallMobile && canCreateRows"
				class="addRowButton"
				:close-after-click="true"
				:aria-label="createRowLabel"
				type="secondary"
				@click="$emit('create-row')">
				<template #icon>
					<Plus :size="25" />
				</template>
			</NcButton>
			<SearchForm v-if="canSearch"
				class="searchForm"
				:search-string="getSearchString"
				@set-search-string="str => $emit('set-search-string', str)" />
			<vueSelect class="limitSelect"
				:options="[5, 20, 50, 100, 250, 1000]"
				:value="rowLimitFilter"
				:clearable="false"
				@input="limit => $emit('update:rowLimitFilter', limit)" />
		</div>
		<div class="optionsRow optionsRow2">
			<div>
				<div v-show="selectedRows.length > 0"
					class="selected-rows-option">
					<div style="padding: 10px; color: var(--color-text-maxcontrast);">
						{{ n('biblio', '%n selected row', '%n selected rows', selectedRows.length, {}) }}
					</div>
					<NcActions type="secondary" :force-title="true" :inline="showFullOptions ? 2 : 0">
						<NcActionButton :closeAfterClick="true"
							@click="exportCsv">
							<template #icon>
								<Export :size="20" />
							</template>
							{{ t('biblio', 'Export CSV') }}
						</NcActionButton>
						<NcActionButton v-if="canDeleteRows"
							:closeAfterClick="true"
							@click="deleteSelectedRows">
							<template #icon>
								<Delete :size="20" />
							</template>
							{{ t('biblio', 'Delete') }}
						</NcActionButton>
						<NcActionButton v-if="!showFullOptions"
							:closeAfterClick="true"
							@click="deselectAllRows">
							<template #icon>
								<Check :size="20" />
							</template>
							{{ t('biblio', 'Uncheck all') }}
						</NcActionButton>
					</NcActions>
				</div>
			</div>
			<PageSelector :page="page"
				:max-page="maxPage"
				@update:page="newPage => $emit('update:page', newPage)" />
		</div>
	</div>
</template>

<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import vueSelect from "vue-select";
import { emit } from "@nextcloud/event-bus";
import Plus from "vue-material-design-icons/Plus.vue";
import Check from "vue-material-design-icons/CheckboxBlankOutline.vue";
import Delete from "vue-material-design-icons/Delete.vue";
import Export from "vue-material-design-icons/Export.vue";
import viewportHelper from "../../mixins/viewportHelper.js";
import SearchForm from "../partials/SearchForm.vue";
import PageSelector from "../partials/PageSelector.vue";

export default {
	name: "Options",

	components: {
		NcActions,
		NcActionButton,
		vueSelect,
		SearchForm,
		NcButton,
		Plus,
		Check,
		Delete,
		Export,
		PageSelector,
	},

	mixins: [viewportHelper],

	props: {
		selectedRows: {
			type: Array,
			default: () => [],
		},
		rows: {
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
		showOptions: {
			type: Boolean,
			default: true,
		},
		viewSetting: {
			type: Object,
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
		canSearch: {
			type: Boolean,
			default: true,
		},
		canDeleteRows: {
			type: Boolean,
			default: true,
		},
		rowLimitFilter: {
			type: Number,
			default: 100,
		},
	},

	data() {
		return {
			optionsDivWidth: null,
		};
	},

	computed: {
		getSelectedRows() {
			const rows = [];
			this.selectedRows.forEach(id => {
				rows.push(this.getRowById(id));
			});
			return rows;
		},
		getSearchString() {
			return this.viewSetting?.searchString || "";
		},
		showFullOptions() {
			 return this.optionsDivWidth > 800;
		},
	},

	created() {
		this.updateOptionsDivWidth();
		window.addEventListener("resize", this.updateOptionsDivWidth);
	},

	methods: {
		updateOptionsDivWidth() {
			this.optionsDivWidth = document.getElementsByClassName("options row")[0]?.offsetWidth;
		},
		exportCsv() {
			this.$emit("download-csv", this.getSelectedRows);
		},
		getRowById(rowId) {
			const index = this.rows.findIndex(row => row.id === rowId);
			return this.rows[index];
		},
		deleteSelectedRows() {
			this.$emit("delete-selected-rows", this.selectedRows);
		},
		deselectAllRows() {
			emit("biblio:selected-rows:deselect", {});
		},
	},
};
</script>

<style scoped lang="scss">

.sticky {
	position: -webkit-sticky; /* Safari */
	position: sticky;
	top: 90px;
	left: 0;
}

.optionsRow {
	display: flex;
	flex-flow: row nowrap;
	height: 48px;
	width: 100%;
	margin-bottom: 5px;
	justify-content: flex-end;
}

.optionsRow2 {
	justify-content: space-between;
}

.selected-rows-option {
	display: flex;
	flex-flow: row nowrap;
	min-width: fit-content;
	white-space: nowrap;
	height: 48px;
	overflow: hidden;
}

.add-padding-left {
	padding-left: calc(var(--default-grid-baseline) * 1);
}

:deep(.counter-bubble__counter) {
	max-width: fit-content;
}

.optionsRow1 {
	display: flex;
	width: 100%;
	align-items: center;
	gap: 10px;

	.searchForm {
		flex-grow: 1;

		:deep(.input-field__main-wrapper) {
			height: 48px !important;
		}
	}

	.addRowButton {
		height: 48px;
	}
}

:deep(.addSearchLimitRow button) {
	min-width: fit-content;
}

.searchAndFilter {
	margin-left: calc(var(--default-grid-baseline) * 3);
	width: auto;
	min-width: 100px;
}

.limitSelect {
	--vs-dropdown-min-width: 100px;
	min-width: 100px;
}

</style>
