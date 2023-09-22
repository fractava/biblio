import { defineStore } from "pinia";
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { showError /*, showSuccess */ } from "@nextcloud/dialogs";

export const useMediumsStore = defineStore("mediums", {
	state: () => ({
		libraries: [],
		selectedLibrary: false,
		mediums: [],
	}),
	actions: {
		selectLibrary(id) {
			this.selectedLibrary = id;
			this.fetchMediums();
		},
		createLibrary(options) {
			return new Promise((resolve, reject) => {
				console.log(options);

				const parameters = {
					name: options.name,
				};

				axios.post(generateUrl("/apps/biblio/libraries"), parameters).then(function(response) {
					console.log(response.data);

					resolve(response.data);
				})
					.catch(function(error) {
						showError(t("biblio", "Could not create libraries"));
						reject(error);
					});
			});
		},
		createMedium(options) {
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
				axios.post(generateUrl(`/apps/biblio/libraries/${this.selectedLibrary}/mediums`), parameters).then(function(response) {
					this.mediums.push({
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
		fetchLibraries() {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl("/apps/biblio/libraries"), {}).then((response) => {
					const libraries = response.data;

					console.log(libraries);

					this.libraries = libraries;
					resolve();
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not libraries"));
					});
			});
		},
		fetchMediums() {
			return new Promise((resolve, reject) => {
				if (!this.selectedLibrary) {
					return;
				}
				axios.get(generateUrl(`/apps/biblio/libraries/${this.selectedLibrary}/mediums`), {
					params: {
						include: "model+fields",
					},
				}).then((response) => {
					const mediums = response.data;

					console.log(mediums);

					for (const medium of mediums) {
						for (const fieldIndex in medium.fields) {
							console.log(medium.fields[fieldIndex]);
							medium.fields[fieldIndex].value = JSON.parse(medium.fields[fieldIndex].value);
						}
					}

					this.mediums = mediums;
					resolve();
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not fetch mediums"));
					});
			});
		},
		updateMediumTitle(options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { title: options.title }).then(function(response) {
					this.getMediumById(options.id).title = options.title;
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update title"));
						reject(error);
					});

				resolve();
			});
		},
		updateMediumFieldsOrder(options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { fieldsOrder: options.fieldsOrder }).then(function(response) {
					this.getMediumById(options.id).fieldsOrder = options.fieldsOrder;
				})
					.catch(function(error) {
						console.error(error);
						showError(t("biblio", "Could not update fields order"));
						reject(error);
					});

				resolve();
			});
		},
		updateMediumField(options) {
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
		deleteMediumField(options) {
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
});
