import Vue from "vue";
import { defineStore } from "pinia";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

import { api } from "../api.js";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: false,
		itemFields: [],
		items: [],
	}),
	actions: {
		/* Collections */
		selectCollection(id) {
			this.selectedCollectionId = id;
			this.fetchItemFields();
			this.fetchItems();
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

		/* Item Fields */
		fetchItemFields() {
			if (!this.selectedCollectionId) {
				return;
			}

			api.getItemFields(this.selectedCollectionId)
				.then((fields) => {
					this.itemFields = fields;
				})
				.catch(() => {
					showError(t("biblio", "Could not fetch item fields"));
				});
		},

		/* Items */
		createItem(options) {
			return new Promise((resolve, reject) => {
				let new_item_id;

				console.log(options);

				/* let fieldValues = [];

				for(let field of options.fields) {
					const { value, ...rest } = field;
					fieldValues.push({
						value: JSON.stringify(value),
						...rest,
					});
				}

				console.log(fieldValues); */

				const parameters = {
					title: options.title,
					//fields: JSON.stringify(fieldValues),
				};
				axios.post(generateUrl(`/apps/biblio/collections/${this.selectedCollectionId}/items`), parameters).then(function(response) {
					this.items.push({
						title: options.title,
						id: response.data.id,
					});

					new_item_id = response.data.id;
					console.log("new_item_id = ", new_item_id);

					resolve(new_item_id);
				})
					.catch(function(error) {
						showError(t("biblio", "Could not create item"));
						reject(error);
					});
			});
		},
		fetchItems() {
			return new Promise((resolve, reject) => {
				if (!this.selectedCollectionId) {
					return;
				}
				axios.get(generateUrl(`/apps/biblio/collections/${this.selectedCollectionId}/items`), {
					params: {
						include: "model+fields",
					},
				}).then((response) => {
					const items = response.data;

					console.log(items);

					for (const item of items) {
						for (const fieldIndex in item.fields) {
							console.log(item.fields[fieldIndex]);
							item.fields[fieldIndex].value = JSON.parse(item.fields[fieldIndex].value);
						}
					}

					this.items = items;
					resolve();
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not fetch items"));
					});
			});
		},
		updateItemTitle(options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/items/${options.id}`), { title: options.title }).then(function(response) {
					this.getItemById(options.id).title = options.title;
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update title"));
						reject(error);
					});

				resolve();
			});
		},
		updateItemFieldsOrder(options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/items/${options.id}`), { fieldsOrder: options.fieldsOrder }).then(function(response) {
					this.getItemById(options.id).fieldsOrder = options.fieldsOrder;
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update fields order"));
						reject(error);
					});

				resolve();
			});
		},
		deleteItemField(options) {
			return axios.delete(generateUrl(`/apps/biblio/item_fields/${options.id}`), {
				params: {
					itemId: options.itemId,
				},
			});
		},
	},
	getters: {
		getItemById: (state) => (id) => {
			return state.items.find(item => item.id == id);
		},
	},
});
