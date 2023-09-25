import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";

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
 */

const transforms = {
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
		itemField.settings = JSON.parse(itemField.settings) || {};
		itemField.includeInList = !!itemField.includeInList;
		return itemField;
	},
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
					const collections = response.data.map(transforms.transformCollection);
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
					resolve(transforms.transformCollection(response.data));
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
					resolve(transforms.transformCollection(response.data));
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
					resolve(transforms.transformCollection(response.data));
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
			axios.get(`/apps/biblio/collections/${collectionId}/item_fields`)
				.then((response) => {
					const itemFields = response.data.map(transforms.transformItemField);
					resolve(itemFields);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection to create an item field in
	  * @param {updateItemFieldParameters} parameters attributes of new item field
	  * @return {Promise<ItemField>}
	  */
	createItemField(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/item_fields`, parameters)
				.then(function(response) {
					resolve(transforms.transformItemField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} itemFieldId id of the item field
	  * @param {updateItemFieldParameters} parameters attributes of new item field
	  * @return {Promise<ItemField>}
	  */
	updateItemField(collectionId, itemFieldId, parameters) {
		return new Promise((resolve, reject) => {
			axios.put(generateUrl(`/collections/${collectionId}/item_fields/${itemFieldId}`), parameters)
				.then(function(response) {
					resolve(response.data);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},
};
