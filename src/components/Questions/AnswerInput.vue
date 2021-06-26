<template>
	<li class="question__item">
		<div class="question__item__pseudoInput" />
		<input
			ref="input"
			v-model="answer.text"
			class="question__input"
			minlength="1"
			type="text"
			@keydown.delete="deleteEntry"
			@keydown.enter.prevent="addNewEntry">

		<!-- Delete answer -->
		<Actions>
			<ActionButton icon="icon-close" @click="deleteEntry">
				{{ t('biblio', 'Delete entry') }}
			</ActionButton>
		</Actions>
	</li>
</template>

<script>
import { showError } from '@nextcloud/dialogs'
import { generateOcsUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import pDebounce from 'p-debounce'
import PQueue from 'p-queue'

import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

import OcsResponse2Data from '../../utils/OcsResponse2Data'

export default {
	name: 'AnswerInput',

	components: {
		Actions,
		ActionButton,
	},

	props: {
		answer: {
			type: Object,
			required: true,
		},
		isDropdown: {
			type: Boolean,
			required: true,
		},
	},

	data() {
		return {
			queue: new PQueue({ concurrency: 1 }),
		}
	},

	methods: {
		/**
		 * Focus the input
		 */
		focus() {
			this.$refs.input.focus()
		},

		/**
		 * Request a new answer
		 */
		addNewEntry() {
			this.$emit('add')
		},

		/**
		 * Emit a delete request for this answer
		 * when pressing the delete key on an empty input
		 *
		 * @param {Event} e the event
		 */
		async deleteEntry(e) {
			if (e.type !== 'click' && this.$refs.input.value.length !== 0) {
				return
			}

			// Dismiss delete key action
			e.preventDefault()

			this.$emit('delete', this.answer.id)
		},
    },
}
</script>

<style lang="scss" scoped>
.question__item {
	position: relative;
	display: inline-flex;
	min-height: 44px;

	// Taking styles from server radio-input items
	&__pseudoInput {
		flex-shrink: 0;
		display: inline-block;
		height: 16px;
		width: 16px !important;
		vertical-align: middle;
		margin: 0 14px 0px 0px;
		border: 1px solid #878787;
		border-radius: 1px;
		// Adjust position manually to match input-checkbox
		position: relative;
		top: 10px;
		border-radius: 50%;

		// Do not show pseudo-icon for dropdowns
		&--dropdown {
			display: none;
		}

		&:hover {
			border-color: var(--color-primary-element);
		}
	}
}

// Using type to have a higher order than the input styling of server
.question__input[type=text] {
	width: 100%;
	// Height 34px + 1px Border
	min-height: 35px;
	margin: 0;
	padding: 0 0;
	border: 0;
	border-bottom: 1px dotted var(--color-border-dark);
	border-radius: 0;
	font-size: 14px;
	position: relative;
}

</style>
