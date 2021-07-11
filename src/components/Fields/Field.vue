<!--
  - @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @author John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<li v-click-outside="disableEdit"
		:class="{ 'field--edit': edit }"
		class="field"
		@click="enableEdit">
		<!-- Drag handle -->
		<div v-if="enableDragHandle"
			class="field__drag-handle icon-drag-handle"
			:class="{'field__drag-handle--shiftup': shiftDragHandle}"
			:aria-label="t('biblio', 'Drag to reorder the fields')" />

		<!-- Header -->
		<div class="field__header">
			<input v-if="(edit || !title) && allowTitleEdit"
				:placeholder="t('biblio', 'Title')"
				:value="title"
				class="field__header-title"
				type="text"
				minlength="1"
				required
				@input="onTitleChange">
			<h3 v-else class="field__header-title" v-text="title" />
			<div v-if="!edit && !fieldValid"
				v-tooltip.auto="t('biblio', 'A title is required!')"
				class="field__header-warning icon-error-color"
				tabindex="0" />
			<Actions v-if="allowDeletion" class="field__header-menu" :force-menu="true">
				<ActionButton icon="icon-delete" @click="onDelete">
					{{ t('forms', 'Delete Field') }}
				</ActionButton>
			</Actions>
		</div>

		<!-- Field content -->
		<slot />
	</li>
</template>

<script>
import { directive as ClickOutside } from 'v-click-outside'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

export default {
	name: 'Field',

	directives: {
		ClickOutside,
	},

	components: {
		Actions,
		ActionButton,
	},

	props: {
		title: {
			type: String,
			required: true,
		},
		allowTitleEdit: {
			type: Boolean,
			default: true,
		},
		allowDeletion: {
			type: Boolean,
			default: true,
		},
		enableDragHandle: {
			type: Boolean,
			default: true,
		},
		shiftDragHandle: {
			type: Boolean,
			default: false,
		},
		edit: {
			type: Boolean,
			required: true,
		},
		contentValid: {
			type: Boolean,
			default: true,
		},
	},

	computed: {

		/**
		 * Field valid, if title not empty and content valid
		 * @returns {Boolean} true if field valid
		 */
		fieldValid() {
			return this.title && this.contentValid
		},
	},

	methods: {
		onTitleChange({ target }) {
			this.$emit('update:title', target.value)
		},

		onRequiredChange(isRequired) {
			this.$emit('update:isRequired', isRequired)
		},

		/**
		 * Enable the edit mode
		 */
		enableEdit() {
			if (!this.allowTitleEdit) {
				this.$emit('update:edit', true)
			}
		},

		/**
		 * Disable the edit mode
		 */
		disableEdit() {
			if (!this.allowTitleEdit) {
				this.$emit('update:edit', false)
			}
		},

		/**
		 * Delete this field
		 */
		onDelete() {
			this.$emit('delete')
		},
	},
}
</script>

<style lang="scss" scoped>
.field {
	position: relative;
	display: flex;
	align-items: stretch;
	flex-direction: column;
	justify-content: stretch;
	margin-bottom: 80px;
	padding-left: 44px;
	user-select: none;
	background-color: var(--color-main-background);

	> * {
		cursor: pointer;
	}

	&__drag-handle {
		position: absolute;
		left: 0;
		width: 44px;
		height: 100%;
		opacity: .5;

		&--shiftup {
			height: calc(100% - 44px);
		}

		&:hover,
		&:focus {
			opacity: 1;
		}
		cursor: grab;

		&:active {
			cursor: grabbing;
		}
	}

	&__title,
	&__content {
		flex: 1 1 100%;
		max-width: 100%;
		padding: 0;
	}

	&__header {
		display: flex;
		align-items: center;
		flex: 1 1 100%;
		justify-content: space-between;
		width: auto;

		// Using type to have a higher order than the input styling of server
		&-title,
		&-title[type=text] {
			flex: 1 1 100%;
			min-height: 22px;
			margin: 0;
			padding: 0;
			padding-bottom: 6px;
			color: var(--color-text-light);
			border: 0;
			border-bottom: 1px dotted transparent;
			border-radius: 0;
			font-size: 16px;
			font-weight: bold;
			line-height: 22px;
		}

		&-title[type=text] {
			border-bottom-color: var(--color-border-dark);
		}

		&-warning {
			padding: 22px;
		}

		&-menu.action-item {
			position: sticky;
			top: var(--header-height);
			// above other actions
			z-index: 50;
		}
	}
}

</style>
