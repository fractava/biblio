import Vue from "vue";
import { defineStore } from "pinia";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

import { useItemsStore } from "./items.js";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: false,
	}),
	actions: {
		/* Collections */
		selectCollection(id) {
			this.selectedCollectionId = id;

			const itemsStore = useItemsStore();
			itemsStore.fetchFields();
			itemsStore.refreshSearchResults();
		},
		fetchCollections() {
			api.getCollections()
				.then((result) => {
					this.collections = result;
				}).catch(() => {
					showError(t("biblio", "Could not fetch collections"));
				});
		},
		createCollection(parameters) {
			return new Promise((resolve, reject) => {
				api.createCollection(parameters)
					.then((result) => {
						this.collections.push(result);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not create collection"));
						resolve();
					});
			});
		},
		updateCollection(id, parameters) {
			return new Promise((resolve, reject) => {
				api.updateCollection(id, parameters)
					.then((result) => {
						const updatedIndex = this.collections.findIndex(collection => collection.id === id);
						Vue.set(this.collections, updatedIndex, result);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not update collection"));
						resolve();
					});
			});
		},
		deleteCollection(id) {
			return new Promise((resolve, reject) => {
				api.deleteCollection(id)
					.then(() => {
						this.collections = this.collections.filter(collection => collection.id !== id);
						resolve();
					}).catch(() => {
						showError(t("biblio", "Could not delete collection"));
						resolve();
					});
			});
		},
	},
	getters: {
		getCollectionById: (state) => (id) => {
			return state.collections.find(collection => collection.id === id);
		},
		selectedCollection: (state) => {
			return state.getCollectionById(state.selectedCollectionId);
		},
	},
});
