<template>
	<div>
		<DataTable :columns="columns"
			:rows="searchResults"
			:page="page"
			:max-page="maxPage"
			:can-create-rows="false"
			:can-search="false"
			:current-sort-reverse="sortReverse"
			:current-filters="filters"
			:current-sort="sort"
			:row-limit-filter="limit"
			@update:currentFilters="onFieltersUpdate"
			@update:currentSort="onSortUpdate"
			@update:currentSortReverse="onSortReverseUpdate"
			@update:rowLimitFilter="onRowLimitFilterUpdate"
			@update:page="onPageUpdate"
			@click-row="onRowClick" />
	</div>
</template>

<script>
import { mapStores } from "pinia";
import PCancelable from "p-cancelable";
import debounceFn from "debounce-fn";
import { showError } from "@nextcloud/dialogs";

import { api } from "../api.js";

import DataTable from "../components/dataTable/DataTable.vue";
import TextCell from "../components/Fields/Cells/TextCell.vue";
import HistoryEntryDescriptionCell from "./HistoryEntryDescriptionCell.vue";
import TimestampCell from "./TimestampCell.vue";

import { useBiblioStore } from "../store/biblio.js";
import { useNomenclatureStore } from "../store/nomenclature.js";

import FieldTypes from "../models/FieldTypes.js";
import { getMaxPage } from "../utils/dataTableHelpers.js";

export default {
	components: {
		DataTable,
	},
	props: {
	},
	data() {
		return {
			searchResults: [],
			searchMeta: {},
			filters: {},
			sort: "timestamp",
			sortReverse: true,
			limit: 100,
			page: 1,
			currentlyRunningFetch: false,
			barcodePrefix: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore, useNomenclatureStore),
		maxPage() {
			return getMaxPage(this.searchMeta.totalResultCount, this.limit);
		},
		columns() {
			return [
				{
					id: -1,
					name: t("biblio", "Description"),
					type: "short",
					isProperty: true,
					canSort: false,
					canFilter: false,
					clickable: false,
					cellComponent: HistoryEntryDescriptionCell,
					// get whole row data, not specific property
					property: [],
					maximizeWidth: true,
				},
				{
					id: -2,
					name: t("biblio", "User"),
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "userId",
					canFilter: true,
					clickable: false,
					cellComponent: TextCell,
					filterOperators: FieldTypes?.short?.filterOperators,
					property: ["userId"],
				},
				{
					id: -3,
					name: t("biblio", "Timestamp"),
					type: "short",
					isProperty: true,
					canSort: true,
					sortIdentifier: "timestamp",
					canFilter: false,
					clickable: false,
					cellComponent: TimestampCell,
					property: ["timestamp"],
				},
			];
		},
	},
	watch: {
		maxPage() {
			this.ensurePageIsNotExceedingMax();
		},
	},
	mounted() {
		this.refreshSearchResults();
	},
	methods: {
		refreshSearchResults() {
			const route = window.biblioRouter.currentRoute;

			if (this.currentlyRunningFetch) {
				this.currentlyRunningFetch.cancel();
			}

			const newHistoryEntriesFetch = new PCancelable((resolve, reject, onCancel) => {
				if (!route.params.collectionId) {
					this.searchResults = [];
					return resolve();
				}

				const offset = (this.page - 1) * this.limit;

				const apiPromise = api.getHistoryEntries(route.params.collectionId, "model+subEntries+item+itemInstance+customer", this.filters, this.sort, this.sortReverse, this.limit, offset);

				apiPromise.then((result) => {
					this.searchResults = result.historyEntries;
					this.searchMeta = result.meta;
					resolve();
				})
					.catch((error) => {
						if (!apiPromise.isCanceled) {
							console.error(error);
							showError(t("biblio", "Could not fetch history"));
						}

						resolve();
					});

				onCancel(() => {
					apiPromise.cancel();
				});
			});

			this.currentlyRunningFetch = newHistoryEntriesFetch;

			return newHistoryEntriesFetch;
		},
		onRowClick(entryId, columnId) {
		},
		onSearchUpdate(newSearch) {
			this.search = newSearch;
			this.refreshSearch();
		},
		onFieltersUpdate(newFilters) {
			this.filters = newFilters;
			this.refreshSearch();
		},
		onSortUpdate(newSort) {
			this.sort = newSort;
			this.refreshSearch();
		},
		onSortReverseUpdate(newSortReverse) {
			this.sortReverse = newSortReverse;
			this.refreshSearch();
		},
		onRowLimitFilterUpdate(newLimit) {
			this.limit = newLimit;
			this.ensurePageIsNotExceedingMax();
			this.refreshSearch();
		},
		onPageUpdate(newPage) {
			this.page = newPage;
			this.refreshSearch();
		},
		refreshSearch: debounceFn(function() {
			const refreshPromise = this.refreshSearchResults();

			refreshPromise.catch((error) => {
				if (!refreshPromise.isCanceled) {
					console.error(error);
				}
			});
		}, { wait: 100 }),
		ensurePageIsNotExceedingMax() {
			if (this.page > this.maxPage) {
				this.page = this.maxPage;
			}
		},
	},
};
</script>

<style lang="scss" scoped>

.section {
	margin-bottom: 1.34em;
}
</style>
