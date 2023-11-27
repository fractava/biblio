import Vue from "vue";
import { defineStore } from "pinia";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: undefined,
		selectedCollectionLoanUntilPresets: [],
	}),
	actions: {
		/* Collections */
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
					}).catch((error) => {
						let errorMessage = t("biblio", "Could not create collection");

						if (error?.response?.data?.error) {
							errorMessage += ": " + error?.response?.data?.error;
						}

						showError(errorMessage);
						resolve();
					});
			});
		},
		updateCollection(id, parameters) {
			return new Promise((resolve, reject) => {
				// optimistic update
				const updatedCollection = this.collections.find(collection => collection.id === id);0
				Object.assign(updatedCollection, parameters);

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

		fetchLoanUntilPresets() {
			if (!this.selectedCollectionId) {
				this.selectedCollectionLoanUntilPresets = [];
				return;
			}

			api.getLoanUntilPresets(this.selectedCollectionId)
				.then((presets) => {
					this.selectedCollectionLoanUntilPresets = presets;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch loan until presets"));
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
