import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'

export default {
	state: () => ({
		mediums: [],
	}),
	mutations: {
		createMedium(state, options) {
            //options.fields = JSON.parse(options.fields);

            this.state.mediums.mediums.push(options);
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
                let parameters = {
                    title: options.title,
                    fields: JSON.stringify(options.fields),
                }
                axios.post(generateUrl('/apps/biblio/mediums'), parameters).then(function(response) {
                    context.commit('createMedium', {
                        title: options.title,
                        fields: options.fields,
                        id: response.data.id,
                    })
                    resolve(response.data.id);
                })
                .catch(function(error) {
                    showError(t('biblio', 'Could not create medium'))
                    reject(error);
                })
				
			})
		},
		fecthMediums(context) {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl('/apps/biblio/mediums')).then(function(response) {
                    let mediums = response.data;

                    for(let medium in mediums) {
                        console.log(medium);
                        mediums[medium].fields = JSON.parse(mediums[medium].fields)
                    }

					context.commit('setMediums', mediums)
					resolve()
				})
					.catch(function(error) {
                        console.error(error);
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
                        console.error(error);
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
                        console.error(error);
						showError(t('biblio', 'Could not update title'))
					})

				resolve()
			})
		},
	},
	getters: {
		getMediumById: (state) => (id) => {
			console.log(state)
			return state.mediums.find(medium => medium.id == id)
		},
	},
}
