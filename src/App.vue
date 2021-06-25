<template>
	<div id="content" class="app-biblio">
		<AppNavigation>
			<AppNavigationItem :title="t('biblio', 'Lend/Return')" icon="icon-medium" />
            <AppNavigationItem :title="t('biblio', 'Mediums')" icon="icon-category-enabled" />
            <AppNavigationItem :title="t('biblio', 'Customers')" icon="icon-category-enabled" />
		</AppNavigation>
		<AppContent>
            <ul>
                <listItem
                    v-for="medium in mediums"
                    :title="medium.title"
                    />
            </ul>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
        ListItem,
	},
	data() {
		return {
			mediums: [],
			currentNoteId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/biblio/mediums'))
			this.mediums = response.data
		} catch (e) {
			console.error(e)
			showError(t('biblio', 'Could not fetch mediums'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote)
			} else {
				this.updateNote(this.currentNote)
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.notes.push({
					id: -1,
					title: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createMedium(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/biblio/mediums'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('biblio', 'Could not create the medium'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateMedium(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/biblio/mediums${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('biblio', 'Could not update the medium'))
			}
			this.updating = false
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteMedium(note) {
			try {
				await axios.delete(generateUrl(`/apps/biblio/mediums/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
				}
				showSuccess(t('biblio', 'Medium deleted'))
			} catch (e) {
				console.error(e)
				showError(t('biblio', 'Could not delete the medium'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>
