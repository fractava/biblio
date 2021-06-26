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
	},
	getters: {},
}
