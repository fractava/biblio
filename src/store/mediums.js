import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'

export default {
	state: () => ({
		mediums: [],
	}),
	mutations: {
		createMedium(state, medium) {

		},
		setMediums(state, mediums) {
			this.state.mediums.mediums = mediums
		},
		updateMediumTitle(state, options) {
			this.getters.getMediumById(options.id).title = options.title
		},
	},
	actions: {
		createMedium() {
			return new Promise((resolve, reject) => {
				resolve()
			})
		},
		fecthMediums(context) {
			return new Promise((resolve, reject) => {
				axios.get(generateUrl('/apps/biblio/mediums')).then(function(response) {
					context.commit('setMediums', response.data)
					resolve()
				})
					.catch(function() {
						showError(t('biblio', 'Could not fetch mediums'))
					})
			})
		},
		updateMediumTitle(context, options) {
			return new Promise((resolve, reject) => {
				axios.put(generateUrl(`/apps/biblio/mediums/${options.id}`), { title: options.title }).then(function(response) {
					context.commit('updateMediumTitle', options)
				})
					.catch(function() {
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
