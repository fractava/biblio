import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

export default {
	state: () => ({
		mediums: [],
	}),
	mutations: {
		createMedium(state, options) {
			this.state.mediums.mediums.push(options);
		},
		setMediums(state, mediums) {
			this.state.mediums.mediums = mediums;
		},
		updateMediumTitle(state, options) {
			this.getters.getMediumById(options.id).title = options.title;
		},
		updateMediumFieldsOrder(state, options) {
			this.getters.getMediumById(options.id).fieldsOrder = options.fieldsOrder;
		},
	},
	actions: {
		createMedium(context, options) {
			return new Promise((resolve, reject) => {
				let new_medium_id;

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
				axios.post(generateUrl("/apps/biblio/mediums"), parameters).then(function(response) {
					context.commit("createMedium", {
						title: options.title,
						id: response.data.id,
					});
					new_medium_id = response.data.id;
					console.log("new_medium_id = ", new_medium_id);

					resolve(new_medium_id);
				})
					.catch(function(error) {
						showError(t("biblio", "Could not create medium"));
						reject(error);
					});
			});
		},
		fecthMediums(context) {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl("/apps/biblio/mediums"), {
					params: {
						include: "model+fields",
					},
				}).then(function(response) {
					const mediums = response.data;

					console.log(mediums);

					for (const medium of mediums) {
						for (const fieldIndex in medium.fields) {
							console.log(medium.fields[fieldIndex]);
							medium.fields[fieldIndex].value = JSON.parse(medium.fields[fieldIndex].value);
						}
					}

					context.commit("setMediums", mediums);
					resolve();
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not fetch mediums"));
					});
			});
		},
		updateMediumTitle(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { title: options.title }).then(function(response) {
					context.commit("updateMediumTitle", options);
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update title"));
						reject(error);
					});

				resolve();
			});
		},
		updateMediumFieldsOrder(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { fieldsOrder: options.fieldsOrder }).then(function(response) {
					context.commit("updateMediumFieldsOrder", options);
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update fields order"));
						reject(error);
					});

				resolve();
			});
		},
		updateMediumField(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/medium_fields/${options.id}`), {
					mediumId: options.mediumId,
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
		deleteMediumField(context, options) {
			return axios.delete(generateUrl(`/apps/biblio/medium_fields/${options.id}`), {
				params: {
					mediumId: options.mediumId,
				},
			});
		},
	},
	getters: {
		getMediumById: (state) => (id) => {
			return state.mediums.find(medium => medium.id == id);
		},
		getMediumFields: (state) => (id) => {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl(`/apps/biblio/medium_fields/${id}`))
					.then((response) => {
						const fields = response.data;

						for (const field of fields) {
							field.value = JSON.parse(field.value);
						}

						const fieldsOrder = state.mediums.find(medium => medium.id == id).fieldsOrder;

						fields.sort(function(a, b) {
							return fieldsOrder.indexOf(a.id) - fieldsOrder.indexOf(b.id);
						});

						resolve(fields);
					})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not fetch medium fields"));
						reject(error);
					});
			});
		},
	},
};
