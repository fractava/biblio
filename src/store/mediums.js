import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError /*, showSuccess */ } from '@nextcloud/dialogs'

export default {
	state: () => ({
		mediums: [],
	}),
	mutations: {
		createMedium(state, options) {
			// options.fields = JSON.parse(options.fields);

			this.state.mediums.mediums.push(options)
		},
		setMediums(state, mediums) {
			this.state.mediums.mediums = mediums
		},
		updateMediumTitle(state, options) {
			this.getters.getMediumById(options.id).title = options.title
		},
		updateMediumFields(state, options) {
			this.getters.getMediumById(options.id).fields = options.fields
		},
	},
	actions: {
		createMedium(context, options) {
			return new Promise((resolve, reject) => {
				let new_medium_id;

				const parameters = {
					title: options.title,
					fields_order: "[]",
				}
				axios.post(generateUrl('/apps/biblio/mediums'), parameters).then(function(response) {
					context.commit('createMedium', {
						title: options.title,
						id: response.data.id,
					})
					new_medium_id = response.data.id;

					for(let field of options.fields) {
						console.log(field);
						console.log(
							new_medium_id,
							field.title,
							field.value,
						);
						axios.post(generateUrl('/apps/biblio/medium_fields'), {
							medium_id: new_medium_id,
							title: field.title,
							value: JSON.stringify(field.value),
						})
						.then(function(response) {})
						.catch(function(error) {
							showError(t('biblio', 'Could not create medium'))
							reject(error)
						})
					}
	
					resolve(new_medium_id);
				})
				.catch(function(error) {
					showError(t('biblio', 'Could not create medium'))
					reject(error)
				})
			})
		},
		fecthMediums(context) {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl('/apps/biblio/mediums')).then(function(response) {
					const mediums = response.data

					for (const medium in mediums) {
						mediums[medium].fieldsOrder = JSON.parse(mediums[medium].fieldsOrder)
					}

					context.commit('setMediums', mediums)
					resolve()
				})
					.catch(function(error) {
						console.error(error)
						showError(t('biblio', 'Could not fetch mediums'))
					})
			})
		},
		updateMediumTitle(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { title: options.title }).then(function(response) {
					context.commit('updateMediumTitle', options)
				})
					.catch(function(error) {
						console.error(error)
						showError(t('biblio', 'Could not update title'))
					})

				resolve()
			})
		},
		updateMediumFields(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { fields: JSON.stringify(options.fields) }).then(function(response) {
					context.commit('updateMediumFields', options)
				})
					.catch(function(error) {
						console.error(error)
						showError(t('biblio', 'Could not update title'))
					})

				resolve()
			})
		},
	},
	getters: {
		getMediumById: (state) => (id) => {
			return state.mediums.find(medium => medium.id == id)
		},
		getMediumFields: (state) => (id) => {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl(`/apps/biblio/medium_fields/${id}`))
				.then(function(response) {
					const fields = response.data;
					resolve(fields);
				})
				.catch(function(error) {
					console.error(error)
					showError(t('biblio', 'Could not fetch medium fields'))
				})
			})
		},
	},
}
