<template>
	<li class="field__item">
		<div class="field__item__pseudoInput" />
		<input
			ref="input"
			v-model="entry.text"
			class="field__input"
			minlength="1"
			type="text"
			:disabled="!allowValueEdit"
			@keydown.delete="deleteEntry"
			@keydown.enter.prevent="addNewEntry">

		<!-- Delete entry -->
		<Actions v-if="allowValueEdit">
			<ActionButton icon="icon-close" @click="deleteEntry">
				{{ t('biblio', 'Delete entry') }}
			</ActionButton>
		</Actions>
	</li>
</template>

<script>
import Actions from "@nextcloud/vue/dist/Components/Actions";
import ActionButton from "@nextcloud/vue/dist/Components/ActionButton";

export default {
	name: "EntryInput",

	components: {
		Actions,
		ActionButton,
	},

	props: {
		entry: {
			type: Object,
			required: true,
		},
		allowValueEdit: {
			type: Boolean,
			default: true,
		},
	},

	methods: {
		/**
		 * Focus the input
		 */
		focus() {
			this.$refs.input.focus();
		},

		/**
		 * Request a new entry
		 */
		addNewEntry() {
			this.$emit("add");
		},

		/**
		 * Emit a delete request for this entry
		 * when pressing the delete key on an empty input
		 *
		 * @param {Event} e the event
		 */
		async deleteEntry(e) {
			if (e.type !== "click" && this.$refs.input.value.length !== 0) {
				return;
			}

			// Dismiss delete key action
			e.preventDefault();

			this.$emit("delete", this.entry.id);
		},
	},
};
</script>

<style lang="scss" scoped>
.field__item {
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
.field__input[type=text] {
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
