import Vue from "vue";
import { defineStore } from "pinia";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

export const useBiblioStore = defineStore("biblio", {
	state: () => ({
		collections: [],
		selectedCollectionId: false,
		items: [],
	}),
	actions: {
		selectCollection(id) {
			this.selectedCollectionId = id;
			this.fetchItems();
		},
		createCollection(options) {
			return new Promise((resolve, reject) => {
				console.log(options);

				const parameters = {
					name: options.name,
				};

				axios.post(generateUrl("/apps/biblio/collections"), parameters).then(function(response) {
					console.log(response.data);

					resolve(response.data);
				})
					.catch(function(error) {
						showError(t("biblio", "Could not create collection"));
						reject(error);
					});
			});
		},
		updateCollection(id, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/collections/${id}`), options).then((response) => {
					const updatedIndex = this.collections.findIndex(collection => collection.id === id);
					Vue.set(this.collections, updatedIndex, response.data);
					// TODO: Update store
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update collection"));
						reject(error);
					});

				resolve();
			});
		},
		deleteCollection(id) {
			return new Promise((resolve, reject) => {
				axios.delete(generateUrl(`/apps/biblio/collections/${id}`)).then(function(response) {
					console.log(response.data);

					resolve(response.data);
				})
					.catch(function(error) {
						showError(t("biblio", "Could not delete collection"));
						reject(error);
					});
			});
		},
		createItem(options) {
			return new Promise((resolve, reject) => {
				let new_item_id;

				console.log(options);

				let fields = [];

				for(let field of options.fields) {
					const { value, ...rest } = field;
					fields.push({
						value: JSON.stringify(value),
						...rest,
					});
				}

				console.log(fields);

				const parameters = {
					title: options.title,
					fields: JSON.stringify(fields),
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
		fetchCollections() {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl("/apps/biblio/collections"), {}).then((response) => {
					const collections = response.data;

					console.log(collections);

					this.collections = collections;
					resolve();
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not collections"));
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
		updateItemField(options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/item_fields/${options.id}`), {
					itemId: options.itemId,
					type: options.type,
					title: options.title,
					value: JSON.stringify(options.value),
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update field"));
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
		getItemFields: (state) => (id) => {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl(`/apps/biblio/item_fields/${id}`))
					.then((response) => {
						const fields = response.data;

						for (const field of fields) {
							field.value = JSON.parse(field.value);
						}

						const fieldsOrder = state.items.find(item => item.id == id).fieldsOrder;

						fields.sort(function(a, b) {
							return fieldsOrder.indexOf(a.id) - fieldsOrder.indexOf(b.id);
						});

						resolve(fields);
					})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not fetch item fields"));
						reject(error);
					});
			});
		},
	},
});
