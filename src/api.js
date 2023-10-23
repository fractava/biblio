import axios from "@nextcloud/axios";
import PCancelable from 'p-cancelable';
import { generateUrl } from "@nextcloud/router";

import FieldTypes from "./models/FieldTypes.js";

axios.defaults.baseURL = generateUrl("/apps/biblio");

/**
 * @typedef {{
 *   id: number
 *   name: string | undefined
 *   itemFieldsOrder: string
 *   customerFieldsOrder: string
 * }} CollectionResponse
 *
 * @typedef {{
 *   id: number
 *   name: string | undefined
 *   itemFieldsOrder: Array<number>
 *   customerFieldsOrder: Array<number>
 * }} Collection
 *
 * @typedef {{
 *   name: string | undefined
 *   itemFieldsOrder: Array<number> | undefined
 *   customerFieldsOrder: Array<number> | undefined
 * }} updateCollectionParameters
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   userId: string
 * }} CollectionMember
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   type: string
 *   name: string
 *   settings: string
 *   includeInList: boolean | number
 * }} FieldResponse
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   type: string
 *   name: string
 *   settings: object
 *   includeInList: boolean
 * }} Field
 *
 * @typedef {{
 *   type: string | undefined
 *   name: string | undefined
 *   settings: object | undefined
 *   includeInList: boolean | undefined
 * }} updateFieldParameters
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
 * }} FieldValueResponse
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
 * }} FieldValue
 *
 * @typedef {{
 *   value: object
 * }} updateFieldValueParameters
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   title: string
 *   fieldValues: Array<FieldValueResponse>
 * }} ItemResponse
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   title: string
 *   fieldValues: Array<FieldValue>
 * }} Item
 *
 * @typedef {{
 *   title: string
 * }} updateItemParameters
 *
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   title: string
 *   fieldValues: Array<FieldValueResponse>
 * }} CustomerResponse
 *
 * @typedef {{
 *   id: number
 *   collectionId: number
 *   name: string
 *   fieldValues: Array<FieldValue>
 * }} Customer
 *
 * @typedef {{
 *   name: string
 * }} updateCustomerParameters
 *
 */

const transforms = {
	fromAPI: {
		/**
		 *
		 * @param {CollectionResponse} collection Collection API Response
		 * @return {Collection}
		 */
		transformCollection(collection) {
			collection.itemFieldsOrder = JSON.parse(collection.itemFieldsOrder) || [];
			collection.customerFieldsOrder = JSON.parse(collection.customerFieldsOrder) || [];
			return collection;
		},
		/**
		 *
		 * @param {FieldResponse} itemField (Item|Customer) Field API Result
		 * @return {Field}
		 */
		transformField(itemField) {
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
		 * @param {FieldValueResponse} fieldValue (Item|Customer) Field API Result
		 * @return {FieldValue}
		 */
		transformFieldValue(fieldValue) {
			fieldValue = transforms.fromAPI.transformField(fieldValue);

			let defaultValue = "";

			if (FieldTypes[fieldValue.type]) {
				defaultValue = FieldTypes[fieldValue.type].defaultValue;
			}

			if (fieldValue.value && fieldValue.value !== "") {
				try {
					fieldValue.value = JSON.parse(fieldValue.value);
				} catch (e) {
					fieldValue.value = defaultValue;
				}
			} else {
				fieldValue.value = defaultValue;
			}

			return fieldValue;
		},

		/**
		 *
		 * @param {ItemResponse} item Item API Result
		 * @return {Item}
		 */
		transformItem(item) {
			if (item.fieldValues) {
				item.fieldValues = item.fieldValues.map(transforms.fromAPI.transformFieldValue);
			}
			return item;
		},

		/**
		 *
		 * @param {CustomerResponse} customer Customer API Result
		 * @return {Customer}
		 */
		transformCustomer(customer) {
			if (customer.fieldValues) {
				customer.fieldValues = customer.fieldValues.map(transforms.fromAPI.transformFieldValue);
			}
			return customer;
		},
	},
	toAPI: {
		/**
		 *
		 * @param {Collection} collection Collection
		 * @return {CollectionResponse}
		 */
		transformCollection(collection) {
			collection.itemFieldsOrder = JSON.stringify(collection.itemFieldsOrder);
			collection.customerFieldsOrder = JSON.stringify(collection.customerFieldsOrder);
			return collection;
		},

		/**
		 *
		 * @param {Field} itemField (Item|Customer) Field
		 * @return {FieldResponse}
		 */
		transformField(itemField) {
			itemField.settings = JSON.stringify(itemField.settings);
			return itemField;
		},

		/**
		 *
		 * @param {FieldValue} fieldValue (Item|Customer) Field Value
		 * @return {FieldValueResponse}
		 */
		transformFieldValue(fieldValue) {
			fieldValue.value = JSON.stringify(fieldValue.value);

			return fieldValue;
		},
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
			parameters = transforms.toAPI.transformCollection(parameters);

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
	  * @param {number} collectionId Id of collection
	  * @return {Promise<Array<CollectionMember>>}
	  */
	getCollectionMembers(collectionId) {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/members`, {})
				.then((response) => {
					resolve(response.data);
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection whose items to fetch
	 * @return {Promise<Array<Field>>}
	 */
	getItemFields: (collectionId) => {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/item_fields`)
				.then((response) => {
					const itemFields = response.data.map(transforms.fromAPI.transformField);
					resolve(itemFields);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to create the item field in
	  * @param {updateFieldParameters} parameters attributes of new item field
	  * @return {Promise<Field>}
	  */
	createItemField(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformField(parameters);
			axios.post(`/collections/${collectionId}/item_fields`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} itemFieldId id of the item field to update
	  * @param {updateFieldParameters} parameters attributes of the item field to update
	  * @return {Promise<Field>}
	  */
	updateItemField(collectionId, itemFieldId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformField(parameters);
			axios.put(`/collections/${collectionId}/item_fields/${itemFieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} itemFieldId id of the item field to delete
	  * @return {Promise<Field>}
	  */
	deleteItemField(collectionId, itemFieldId) {
		return new Promise((resolve, reject) => {
			return axios.delete(`/collections/${collectionId}/item_fields/${itemFieldId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection whose items to fetch
	 * @return {Promise<Array<Field>>}
	 */
	getCustomerFields: (collectionId) => {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/customer_fields`)
				.then((response) => {
					const itemFields = response.data.map(transforms.fromAPI.transformField);
					resolve(itemFields);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to create the item field in
	  * @param {updateFieldParameters} parameters attributes of new item field
	  * @return {Promise<Field>}
	  */
	createCustomerField(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformField(parameters);
			axios.post(`/collections/${collectionId}/customer_fields`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} customerFieldId id of the customer field to update
	  * @param {updateFieldParameters} parameters attributes of the item field to update
	  * @return {Promise<Field>}
	  */
	updateCustomerField(collectionId, customerFieldId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformField(parameters);
			axios.put(`/collections/${collectionId}/customer_fields/${customerFieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item field is in
	  * @param {number} customerFieldId id of the item field to delete
	  * @return {Promise<Field>}
	  */
	deleteCustomerField(collectionId, customerFieldId) {
		return new Promise((resolve, reject) => {
			return axios.delete(`/collections/${collectionId}/customer_fields/${customerFieldId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformField(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to get the items of
	  * @param {string} include information the server should include in the returned API object
	  * @param {object} filters filters result on server side
	  * @param {string} sort column the result will be sorted by
	  * @param {boolean} sortReverse wether to reverse the sort direction
	  * @param {number} limit limit the number of results returned
	  * @param {number} offset the offset of the results returned
	  * @return {PCancelable<Array<Item>>}
	  */
	getItems(collectionId, include = "model+fields", filters = {}, sort = "", sortReverse = false, limit = 0, offset = 0) {
		return new PCancelable((resolve, reject, onCancel) => {
			const controller = new AbortController();

			onCancel(() => {
				controller.abort()
			});

			axios.get(`/collections/${collectionId}/items`, {
				signal: controller.signal,
				params: {
					include,
					filter: JSON.stringify(filters),
					sort,
					sortReverse,
					limit,
					offset,
				},
			})
				.then((response) => {
					const items = response.data.result.map(transforms.fromAPI.transformItem);
					resolve({
						meta: response.data.meta,
						items,
					});
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} itemId Id of the item
      * @param {string} include information the server should include in the returned API object
	  * @return {Promise<Item>}
	  */
	getItem(collectionId, itemId, include = "model+fields") {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/items/${itemId}`, {
				params: {
					include,
				},
			})
				.then((response) => {
					resolve(transforms.fromAPI.transformItem(response.data));
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
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} itemId Id of the item to delete
	  * @return {Promise<Item>}
	  */
	deleteItem(collectionId, itemId) {
		return new Promise((resolve, reject) => {
			axios.delete(`/collections/${collectionId}/items/${itemId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformItem(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection to get the customers of
	  * @param {string} include information the server should include in the returned API object
	  * @param {object} filters filters result on server side
	  * @param {string} sort column the result will be sorted by
	  * @param {boolean} sortReverse wether to reverse the sort direction
	  * @param {number} limit limit the number of results returned
	  * @param {number} offset the offset of the results returned
	  * @return {PCancelable<Array<Customer>>}
	  */
	getCustomers(collectionId, include = "model+fields", filters = {}, sort = "", sortReverse = false, limit = 0, offset = 0) {
		return new PCancelable((resolve, reject, onCancel) => {
			const controller = new AbortController();

			onCancel(() => {
				controller.abort();
			});

			axios.get(`/collections/${collectionId}/customers`, {
				signal: controller.signal,
				params: {
					include,
					filter: JSON.stringify(filters),
					sort,
					sortReverse,
					limit,
					offset,
				},
			})
				.then((response) => {
					const customers = response.data.result.map(transforms.fromAPI.transformCustomer);
					resolve({
						meta: response.data.meta,
						customers,
					});
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection the customer is in
	 * @param {number} customerId Id of the customer
	 * @param {string} include information the server should include in the returned API object
	 * @return {Promise<Customer>}
	 */
	getCustomer(collectionId, customerId, include = "model+fields") {
		return new Promise((resolve, reject) => {
			axios.get(`/collections/${collectionId}/customers/${customerId}`, {
				params: {
					include,
				},
			})
				.then((response) => {
					resolve(transforms.fromAPI.transformCustomer(response.data));
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection to create the customer in
	 * @param {updateCustomerParameters} parameters attributes of new customer
	 * @return {Promise<Customer>}
	 */
	createCustomer(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/customers`, parameters)
				.then(function(response) {
					const customer = transforms.fromAPI.transformCustomer(response.data);
					resolve(customer);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection the customer is in
	 * @param {number} customerId Id of the customer
	 * @param {updateCustomerParameters} parameters attributes of the customer to update
	 * @return {Promise<Customer>}
	 */
	updateCustomer(collectionId, customerId, parameters) {
		return new Promise((resolve, reject) => {
			axios.put(`/collections/${collectionId}/customers/${customerId}`, parameters)
				.then(function(response) {
					const customer = transforms.fromAPI.transformCustomer(response.data);
					resolve(customer);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection the customer is in
	 * @param {number} customerId Id of the customer to delete
	 * @return {Promise<Customer>}
	 */
	deleteCustomer(collectionId, customerId) {
		return new Promise((resolve, reject) => {
			axios.delete(`/collections/${collectionId}/customers/${customerId}`)
				.then(function(response) {
					resolve(transforms.fromAPI.transformCustomer(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	 /**
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} itemId Id of the item the field value is for
	  * @param {number} fieldId Id of the field the item field value is for
	  * @param {updateFieldValueParameters} parameters attributes of item field value to update
	  * @return {Promise<FieldValue>}
	  */
	updateItemFieldValue(collectionId, itemId, fieldId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformFieldValue(parameters);

			axios.put(`/collections/${collectionId}/items/${itemId}/field_values/${fieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformFieldValue(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	  * @param {number} collectionId Id of the collection the item is in
	  * @param {number} customerId Id of the customer the field value is for
	  * @param {number} fieldId Id of the field the item field value is for
	  * @param {updateFieldValueParameters} parameters attributes of item field value to update
	  * @return {Promise<FieldValue>}
	  */
	updateCustomerFieldValue(collectionId, customerId, fieldId, parameters) {
		return new Promise((resolve, reject) => {
			parameters = transforms.toAPI.transformFieldValue(parameters);

			axios.put(`/collections/${collectionId}/customers/${customerId}/field_values/${fieldId}`, parameters)
				.then(function(response) {
					resolve(transforms.fromAPI.transformFieldValue(response.data));
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

		 /**
	  * @param {number} collectionId Id of the collection to get the item instances of
	  * @param {string} include information the server should include in the returned API object
	  * @param {object} filters filters result on server side
	  * @param {string} sort column the result will be sorted by
	  * @param {boolean} sortReverse wether to reverse the sort direction
	  * @param {number} limit limit the number of results returned
	  * @param {number} offset the offset of the results returned
	  * @return {PCancelable<Array<ItemInstance>>}
	  */
	getItemInstances(collectionId, include = "model+item+loan+fields", filters = {}, sort = "", sortReverse = false, limit = 0, offset = 0) {
		return new PCancelable((resolve, reject, onCancel) => {
			const controller = new AbortController();

			onCancel(() => {
				controller.abort();
			});

			axios.get(`/collections/${collectionId}/itemInstances`, {
				signal: controller.signal,
				params: {
					include,
					filter: JSON.stringify(filters),
					sort,
					sortReverse,
					limit,
					offset,
				},
			})
				.then((response) => {
					const instances = response.data.result; // .map(transforms.fromAPI.transformItemInstance);
					resolve({
						meta: response.data.meta,
						instances,
					});
				})
				.catch((error) => {
					reject(error);
				});
		});
	},

	createItemInstance(collectionId, parameters) {
		return new Promise((resolve, reject) => {
			axios.post(`/collections/${collectionId}/itemInstances`, parameters)
				.then(function(response) {
					resolve(response.data);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	/**
	 * @param {number} collectionId Id of the collection the item instance is in
	 * @param {number} itemInstanceId Id of the item instance to delete
	 * @return {Promise<ItemInstance>}
	 */
	deleteItemInstance(collectionId, itemInstanceId) {
		return new Promise((resolve, reject) => {
			axios.delete(`/collections/${collectionId}/itemInstances/${itemInstanceId}`)
				.then(function(response) {
					// resolve(transforms.fromAPI.transformItemInstance(response.data));
					resolve(response.data);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},

	importV1(data) {
		return new Promise((resolve, reject) => {
			axios.post("/import/v1", { data })
				.then(function(response) {
					resolve(response);
				})
				.catch(function(error) {
					reject(error);
				});
		});
	},
};
