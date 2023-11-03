<template>
	<div>
		<DataTable class="itemInstancesDataTable"
			:columns="columns"
			:rows="itemInstancesStore.searchResults"
			:page="itemInstancesStore.page"
			:max-page="maxPage"
			:can-create-rows="false"
			:current-sort="itemInstancesStore.sort"
			:current-sort-reverse="itemInstancesStore.sortReverse"
			:current-filters="itemInstancesStore.filters"
			:row-limit-filter="itemInstancesStore.limit"
			@set-search-string="onSearchUpdate"
			@update:currentFilters="onFieltersUpdate"
			@update:currentSort="onSortUpdate"
			@update:currentSortReverse="onSortReverseUpdate"
			@update:rowLimitFilter="onRowLimitFilterUpdate"
			@update:page="onPageUpdate"
			@click-row="openItemInstance"
			@delete-selected-rows="deleteItemInstances" />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import DataTable from "../components/dataTable/DataTable.vue";

import { useItemInstancesStore } from "../store/itemInstances.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

import { getMaxPage, getItemInstanceColumns } from "../utils/dataTableHelpers.js";

export default {
	components: {
		DataTable,
	},
	computed: {
		...mapStores(useItemInstancesStore, useNomenclatureStore),
		maxPage() {
			return getMaxPage(this.itemInstancesStore.searchMeta.totalResultCount, this.itemInstancesStore.limit);
		},
		columns() {
			return getItemInstanceColumns(true, true, true, this.itemInstancesStore.sortedFields);
		},
	},
	watch: {
		maxPage() {
			this.ensurePageIsNotExceedingMax();
		},
	},
	mounted() {
		this.itemInstancesStore.refreshSearchResults();
	},
	methods: {
		openItemInstance(itemInstanceId, columnId) {
			const itemInstance = this.itemInstancesStore.getItemInstanceById(itemInstanceId);
			if (columnId === -2) {
				this.$router.push({
					name: "item",
					params: {
						...this.$route.params,
						id: itemInstance.itemId,
					},
				});
			} else if (columnId === -3) {
				if (itemInstance.loan.customerId) {
					this.$router.push({
						name: "customer",
						params: {
							...this.$route.params,
							id: itemInstance.loan.customerId,
						},
					});
				}
			}
		},
		onSearchUpdate(newSearch) {
			this.itemInstancesStore.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.itemInstancesStore.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.itemInstancesStore.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.itemInstancesStore.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.itemInstancesStore.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.itemInstancesStore.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(function() {
			const itemInstancesStore = useItemInstancesStore();

			const refreshPromise = itemInstancesStore.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.itemInstancesStore.page > this.maxPage) {
				this.itemInstancesStore.page = this.maxPage;
			}
		},
		deleteItemInstances(itemInstanceIds) {
			for (const itemInstanceId of itemInstanceIds) {
				this.itemInstancesStore.deleteItemInstance(itemInstanceId);
			}
		},
	},
};

</script>
<style scoped>
.itemInstancesDataTable {
	margin-bottom: 330px;
}
</style>
