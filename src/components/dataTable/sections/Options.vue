<template>
	<div class="options">
		<div v-if="showOptions" class="fix-col-4" style="display: flex; flex-flow: column;">
			<div :style="{'opacity': (selectedRows.length > 0) ? 1 : 0}" class="selected-rows-option">
				<div style="padding: 10px; color: var(--color-text-maxcontrast);">
					{{ n('biblio', '%n selected row', '%n selected rows', selectedRows.length, {}) }}
				</div>
				<NcActions type="secondary" :force-title="true" :inline="showFullOptions ? 2 : 0">
					<NcActionButton @click="exportCsv">
						<template #icon>
							<Export :size="20" />
						</template>
						{{ t('biblio', 'Export CSV') }}
					</NcActionButton>
					<NcActionButton v-if="canDeleteRows"
						@click="deleteSelectedRows">
						<template #icon>
							<Delete :size="20" />
						</template>
						{{ t('biblio', 'Delete') }}
					</NcActionButton>
					<NcActionButton v-if="!showFullOptions" @click="deselectAllRows">
						<template #icon>
							<Check :size="20" />
						</template>
						{{ t('biblio', 'Uncheck all') }}
					</NcActionButton>
				</NcActions>
			</div>
			<div :class="{'add-padding-left': isSmallMobile }"
				class="addSearchLimitRow">
				<NcButton v-if="!isSmallMobile && canCreateRows"
					:aria-label="createRowLabel"
					:close-after-click="true"
					type="tertiary"
					@click="$emit('create-row')">
					{{ createRowLabel }}
					<template #icon>
						<Plus :size="25" />
					</template>
				</NcButton>
				<NcButton v-if="isSmallMobile && canCreateRows"
					:close-after-click="true"
					:aria-label="createRowLabel"
					type="tertiary"
					@click="$emit('create-row')">
					<template #icon>
						<Plus :size="25" />
					</template>
				</NcButton>
				<div class="searchAndFilter">
					<SearchForm :search-string="getSearchString"
						@set-search-string="str => $emit('set-search-string', str)" />
				</div>
				<NcSelect :options="[20, 50, 100, 250, 1000]" :value="rowLimitFilter" @input="limit => $emit('update:rowLimitFilter', limit)" />
			</div>
		</div>
	</div>
</template>

<script>
import { NcButton, NcActions, NcActionButton } from "@nextcloud/vue";
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect.js'
import { emit } from "@nextcloud/event-bus";
import Plus from "vue-material-design-icons/Plus.vue";
import Check from "vue-material-design-icons/CheckboxBlankOutline.vue";
import Delete from "vue-material-design-icons/Delete.vue";
import Export from "vue-material-design-icons/Export.vue";
import viewportHelper from "../../mixins/viewportHelper.js";
import SearchForm from "../partials/SearchForm.vue";

export default {
	name: "Options",

	components: {
		NcActions,
		NcActionButton,
		NcSelect,
		SearchForm,
		NcButton,
		Plus,
		Check,
		Delete,
		Export,
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

.selected-rows-option {
	display: inline-flex;
	white-space: nowrap;
	overflow: hidden;
	min-width: fit-content;
}

.add-padding-left {
	padding-left: calc(var(--default-grid-baseline) * 1);
}

:deep(.counter-bubble__counter) {
	max-width: fit-content;
}

.addSearchLimitRow {
	display: inline-flex;
	align-items: center;
}

:deep(.addSearchLimitRow button) {
	min-width: fit-content;
}

.searchAndFilter {
	margin-left: calc(var(--default-grid-baseline) * 3);
	width: auto;
	min-width: 100px;
}

</style>
