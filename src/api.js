import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";

import FieldTypes from "./models/FieldTypes.js";

axios.defaults.baseURL = generateUrl("/apps/biblio");

/**
 * @typedef {{
 *   id: number
 *   name: string | undefined
 *   fieldsOrder: string
 * }} CollectionResponse
 *
 * @typedef {{
 *   id: number
 *   name: string | undefined
 *   fieldsOrder: Array<number>
 * }} Collection
 *
 * @typedef {{
 *   name: string | undefined
 *   fieldsOrder: Array<number> | undefined
 * }} updateCollectionParameters
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   type: string
 *   name: string
 *   settings: string
 *   includeInList: boolean | number
 * }} ItemFieldResponse
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   type: string
 *   name: string
 *   settings: object
 *   includeInList: boolean
 * }} ItemField
 *
 * @typedef {{
 *   type: string | undefined
 *   name: string | undefined
 *   settings: object | undefined
 *   includeInList: boolean | undefined
 * }} updateItemFieldParameters
 *
 * @typedef {{
 *   collectionId: number
 *   fieldId: number
 *   itemId: number
 *   type: string
 *   name: string
 *   settings: object
 *   includeInList: boolean
 *   value: string
 * }} ItemFieldValueResponse
 *
 * @typedef {{
 *   collectionId: number
 *   fieldId: number
 *   itemId: number
 *   type: string
 *   name: string
 *   settings: object
 *   includeInList: boolean
 *   value: object
 * }} ItemFieldValue
 *
 * @typedef {{
 *   value: object
 * }} updateItemFieldValueParameters
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   title: string
 *   fieldValues: Array<ItemFieldValueResponse>
 * }} ItemResponse
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   title: string
 *   fieldValues: Array<ItemFieldValue>
 * }} Item
 *
 * @typedef {{
 *   title: string
 * }} updateItemParameters
 */

const transforms = {
	fromAPI: {
		/**
		 *
		 * @param {CollectionResponse} collection Collection API Item
		 * @return {Collection}
		 */
		transformCollection(collection) {
			collection.fieldsOrder = JSON.parse(collection.fieldsOrder) || [];
			return collection;
		},
		/**
		 *
		 * @param {ItemFieldResponse} itemField Item Field API Item
		 * @return {ItemField}
		 */
		transformItemField(itemField) {
			if (itemField.settings && itemField.settings !== "") {
				itemField.settings = JSON.parse(itemField.settings);
			} else if (FieldTypes[itemField.type]) {
				itemField.settings = FieldTypes[itemField.type].defaultSettings;
			} else {
				itemField.settings = "";
			}

			itemField.includeInList = !!itemField.includeInList;
			return itemField;
		},

		/**
		 *
		 * @param {ItemFieldValueResponse} itemFieldValue Item Field API Item
		 * @return {ItemFieldValue}
		 */
		transformItemFieldValue(itemFieldValue) {
			itemFieldValue = transforms.fromAPI.transformItemField(itemFieldValue);

			let defaultValue = "";

			if (FieldTypes[itemFieldValue.type]) {
				defaultValue = FieldTypes[itemFieldValue.type].defaultValue;
			}

			if (itemFieldValue.value && itemFieldValue.value !== "") {
				try {
					itemFieldValue.value = JSON.parse(itemFieldValue.value);
				} catch (e) {
					itemFieldValue.value = defaultValue;
				}
			} else {
				itemFieldValue.value = defaultValue;
			}

			return itemFieldValue;
		},

		/**
		 *
		 * @param {ItemResponse} item Item API Item
		 * @return {Item}
		 */
		transformItem(item) {
			if (item.fieldValues) {
				item.fieldValues = item.fieldValues.map(transforms.fromAPI.transformItemFieldValue);
			}
			return item;
		},
	},
	toAPI: {
		/**
		 *
		 * @param {ItemFieldValue} itemFieldValue Item Field API Item
		 * @return {ItemFieldValueResponse}
		 */
		transformItemFieldValue(itemFieldValue) {
			itemFieldValue.value = JSON.stringify(itemFieldValue.value);

			return itemFieldValue;
		},
	}
};

export const api = {
	/**
	  *
	  * @return {Promise<Array<Collection>>}
	  */
	getCollections() {
		return new Promise((resolve, reject) => {
			axios.get("/collections", {})
				.then((response) => {
					const collections = response.data.map(transforms.fromAPI.transformCollection);
					resolve(collections);
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	  * @param {updateCollectionParameters} parameters attributes of new collection
	  * @return {Promise<Collection>}
	  */
	createCollection(parameters) {
		return new Promise((resolve, reject) => {
			axios.post("/collections", parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformCollection(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of collection to update
	  * @param {updateCollectionParameters} parameters attributes to update
	  * @return {Promise<Collection>}
	  */
	updateCollection(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.put(`/collections/${collectionId}`, parameters)
				.then((response) => {
					resolve(transforms.fromAPI.transformCollection(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of collection to update
	  * @return {Promise<Collection>}
	  */
	deleteCollection(collectionId) {
		return new Promise((resolve, reject) => {
			axios.delete(`/collections/${collectionId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformCollection(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection whose items to fetch
	 * @return {Promise<Array<ItemField>>}
	 */
	getItemFields: (collectionId) => {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/item_fields`)
				.then((response) => {
					const itemFields = response.data.map(transforms.fromAPI.transformItemField);
					resolve(itemFields);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to create the item field in
	  * @param {updateItemFieldParameters} parameters attributes of new item field
	  * @return {Promise<ItemField>}
	  */
	createItemField(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/item_fields`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItemField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} itemFieldId id of the item field to update
	  * @param {updateItemFieldParameters} parameters attributes of the item field to update
	  * @return {Promise<ItemField>}
	  */
	updateItemField(collectionId, itemFieldId, parameters) {
		return new Promise((resolve, reject) => {
			axios.put(`/collections/${collectionId}/item_fields/${itemFieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItemField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} itemFieldId id of the item field to delete
	  * @return {Promise<ItemField>}
	  */
	deleteItemField(collectionId, itemFieldId) {
		return new Promise((resolve, reject) => {
			return axios.delete(`/collections/${collectionId}/item_fields/${itemFieldId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItemField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to get the items of
	  * @param {string} include information the server should include in the returned API object
	  * @return {Promise<Array<Item>>}
	  */
	getItems(collectionId, include = "model+fields") {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/items`, {
				params: {
					include,
				},
			})
				.then((response) => {
					const items = response.data.map(transforms.fromAPI.transformItem);
					resolve(items);
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection to create the item in
	  * @param {updateItemParameters} parameters attributes of new item
	  * @return {Promise<Item>}
	  */
	createItem(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/items`, parameters)
				.then(function(response) {
					const item = transforms.fromAPI.transformItem(response.data);
					resolve(item);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} itemId Id of the item
	  * @param {updateItemParameters} parameters attributes of the item to update
	  * @return {Promise<Item>}
	  */
	updateItem(collectionId, itemId, parameters) {
		return new Promise((resolve, reject) => {
			axios.put(`/collections/${collectionId}/items/${itemId}`, parameters)
				.then(function(response) {
					const item = transforms.fromAPI.transformItem(response.data);
					resolve(item);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to create the item field in
	  * @param {number} itemId Id of the item to create the field value with
	  * @param {updateItemFieldValueParameters} parameters attributes of new item field value
	  * @return {Promise<ItemFieldValue>}
	  */
	createItemFieldValue(collectionId, itemId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/items/${itemId}/field_values`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItemFieldValue(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} itemId Id of the item the field value is for
	  * @param {number} fieldId Id of the field the field value is for
	  * @param {updateItemFieldValueParameters} parameters attributes of item field value to update
	  * @return {Promise<ItemFieldValue>}
	  */
	updateItemFieldValue(collectionId, itemId, fieldId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformItemFieldValue(parameters);

			axios.put(`/collections/${collectionId}/items/${itemId}/field_values/${fieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItemField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	getItemInstances(collectionId, itemId) {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/items/${itemId}/instances`)
				.then(function(response) {
					resolve(response.data);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	createItemInstance(collectionId, itemId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/items/${itemId}/instances`, parameters)
				.then(function(response) {
					resolve(response.data);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},
};
