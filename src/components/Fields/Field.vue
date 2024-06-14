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
	<FieldsTableRow :enable-drag-handle="enableDragHandle" :enable-include-in-list="enableIncludeInList">
		<template #icon>
			<slot name="icon" />
		</template>
		<template #includeInList>
			<NcCheckboxRadioSwitch :checked="includeInList"
				@update:checked="onIncludeInListChange" />
		</template>
		<template #name>
			<NcTextField :label="t('biblio', 'Name')"
				:placeholder="t('biblio', 'Enter a name with at least 3 characters')"
				:value="name"
				class="field__header-name"
				type="text"
				minlength="3"
				:show-trailing-button="false"
				:error="!nameValid"
				:helper-text="nameValid ? '' : t('biblio', 'Invalid name')"
				@input="onNameChange" />
		</template>
		<template #settings>
			<slot name="settings" />
		</template>
		<template #actions>
			<NcActions>
				<NcActionButton @click="onDelete">
					<template #icon>
						<Delete :size="20" />
					</template>
					{{ t('biblio', 'Delete Field') }}
				</NcActionButton>
			</NcActions>
		</template>
	</FieldsTableRow>
</template>

<script>
import { directive as ClickOutside } from "v-click-outside";
import debounceFn from "debounce-fn";

import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'

import Delete from "vue-material-design-icons/Delete.vue";

import FieldsTableRow from "../Settings/FieldsTableRow.vue";

export default {
	name: "Field",

	directives: {
		ClickOutside,
	},

	components: {
		NcActions,
		NcActionButton,
		NcTextField,
		NcCheckboxRadioSwitch,
		Delete,
		FieldsTableRow,
	},

	props: {
		name: {
			type: String,
			required: true,
		},
		includeInList: {
			type: Boolean,
			default: false,
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
		enableIncludeInList: {
			type: Boolean,
			default: true,
		},
		contentValid: {
			type: Boolean,
			default: true,
		},
	},

	computed: {

		/**
		 * Field valid, if name not under 3 characters
		 *
		 * @return {boolean} true if field name valid
		 */
		nameValid() {
			return this.name && this.name.length >= 3;
		},
	},

	methods: {
		onNameChange: debounceFn(function({ target }) {
			this.$emit("update:name", target.value);
		}, { wait:  200}),

		onIncludeInListChange(includeInList) {
			this.$emit("update:includeInList", includeInList);
		},

		/**
		 * Enable the edit mode
		 */
		enableEdit() {
			if (!this.allowNameEdit) {
				this.$emit("update:edit", true);
			}
		},

		/**
		 * Disable the edit mode
		 */
		disableEdit() {
			if (!this.allowNameEdit) {
				this.$emit("update:edit", false);
			}
		},

		/**
		 * Delete this field
		 */
		onDelete() {
			this.$emit("delete");
		},
	},
};
</script>

<style lang="scss" scoped>
.field {
	position: relative;
	display: flex;
	align-items: stretch;
	flex-direction: column;
	justify-content: stretch;
	margin-bottom: 20px;
	padding-left: 44px;
	user-select: none;
	background-color: var(--color-main-background);

	> * {
		cursor: pointer;
	}

	&__border {
		border-collapse: collapse;
		border: 1px solid;
		border-color: rgba(0, 0, 0, 0.38);
		border-radius: 4px;

		padding: 10px;
	}

	&__name,
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
		&-name,
		&-name[type=text] {
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

		&-name[type=text] {
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
