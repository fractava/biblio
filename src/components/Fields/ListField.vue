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
	<Field
		v-bind.sync="$attrs"
		:title="title"
		:edit.sync="edit"
		:allow-title-edit="allowTitleEdit"
		:allow-deletion="allowDeletion"
		:enable-drag-handle="enableDragHandle"
		:title-placeholder="t('biblio', 'Title')"
		:shift-drag-handle="shiftDragHandle"
		@update:title="onTitleChange"
		@delete="onDelete">
		<ul class="field__content">
			<template v-for="(entry, index) in value">
				<li v-if="!edit" :key="entry.id" class="field__item">
					<!-- entry radio/checkbox + label -->
					<!-- TODO: migrate to radio/checkbox component once available -->
					<input :id="`entry-${entry.id}`"
						ref="checkbox"
						class="radio field__radio"
						:name="`${entry.id}-entry`"
						type="radio"
						@change="onChange($event, entry.id)"
						@keydown.enter.exact.prevent="onKeydownEnter">
					<label v-if="!edit"
						ref="label"
						:for="`entry-${entry.id}`"
						class="field__label">{{ entry.text }}</label>
				</li>

				<!-- entry text input edit -->
				<EntryInput v-else
					:key="index /* using index to keep the same vnode after new entry creation */"
					ref="input"
					:entry="entry"
					:index="index"
					:is-dropdown="false"
					:allow-value-edit="allowValueEdit"
					@add="addNewEntry"
					@delete="deleteEntry"
					@update:entry="updateEntry" />
			</template>

			<li v-if="((edit && !isLastEmpty) || hasNoEntry) && allowValueEdit" class="field__item">
				<div class="field__item__pseudoInput" />
				<input
					:aria-label="t('biblio', 'Add a new entry')"
					:placeholder="t('biblio', 'Add a new entry')"
					class="field__input"
					minlength="1"
					type="text"
					@click="addNewEntry"
					@focus="addNewEntry">
			</li>
		</ul>
	</Field>
</template>

<script>
import EntryInput from "./EntryInput";
import FieldMixin from "../../mixins/FieldMixin";
import GenRandomId from "../../utils/GenRandomId";

// Implementations docs
// https://www.w3.org/TR/2016/WD-wai-aria-practices-1.1-20160317/examples/radio/radio.html
// https://www.w3.org/TR/2016/WD-wai-aria-practices-1.1-20160317/examples/checkbox/checkbox-2.html
export default {
	name: "ListField",

	components: {
		EntryInput,
	},

	mixins: [FieldMixin],

	computed: {
		isLastEmpty() {
			const value = this.value[this.value.length - 1];
			return value?.text?.trim().length === 0;
		},

		hasNoEntry() {
			return this.value.length === 0;
		},

		areNoneChecked() {
			return this.value.length === 0;
		},

		shiftDragHandle() {
			return this.edit && this.value.length !== 0 && !this.isLastEmpty;
		},
	},

	watch: {
		edit(edit) {
			// When leaving edit mode, filter and delete empty value
			if (!edit) {
				const value = this.value.filter(entry => entry.text);

				// update parent
				this.updateValue(value);
			}
		},
	},

	methods: {
		onChange(event, entryId) {
			this.$emit("update:value", [entryId]);
		},

		/**
		 * Update the value
		 * @param {Array} value entries to change
		 */
		updateValue(value) {
			this.$emit("update:value", value);
		},

		/**
		 * Update an existing entry
		 *
		 * @param {string|number} id the entry id
		 * @param {Object} entry to update
		 */
		updateEntry(id, entry) {
			const value = this.value.slice();
			const entryIndex = value.findIndex(entry => entry.id === id);
			value[entryIndex] = entry;

			this.updateValue(value);
		},

		/**
		 * Add a new empty entry
		 */
		addNewEntry() {
			// If entering from non-edit-mode (possible by click), activate edit-mode
			this.edit = true;

			// Add entry
			const value = this.value.slice();
			value.push({
				id: GenRandomId(),
				text: "",
			});

			// Update field
			this.updateValue(value);

			this.$nextTick(() => {
				this.focusIndex(value.length - 1);
			});
		},

		/**
		 * Delete an entry
		 *
		 * @param {number} id the entry id
		 */
		deleteEntry(id) {
			const value = this.value.slice();
			const entryIndex = value.findIndex(entry => entry.id === id);

			if (value.length === 1) {
				// Clear Text, but don't remove. Will be removed, when leaving edit-mode
				value[0].text = "";
			} else {
				value.splice(entryIndex, 1);
			}

			// Update field
			this.updateValue(value);

			this.$nextTick(() => {
				this.focusIndex(entryIndex - 1);
			});
		},

		/**
		 * Focus the input matching the index
		 *
		 * @param {Number} index the value index
		 */
		focusIndex(index) {
			const inputs = this.$refs.input;
			if (inputs && inputs[index]) {
				const input = inputs[index];
				input.focus();
			}
		},
	},
};
</script>

<style lang="scss" scoped>
.field__content {
	display: flex;
	flex-direction: column;
}

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
		// Adjust position manually to match pseudo-input and proper position to text
		position: relative;
		top: 10px;
		border-radius: 50%;

		&:hover {
			border-color: var(--color-primary-element);
		}
	}

	.field__label {
		flex: 1 1 100%;
		// Overwrite guest page core styles
		text-align: left !important;
		// Some rounding issues lead to this strange number, so label and EntryInput show up a the same position, working on different browsers.
		padding: 6.5px 0 0 30px;
		line-height: 22px;
		min-height: 34px;
		height: min-content;
		position: relative;

		&::before {
			box-sizing: border-box;
			// Adjust position manually for proper position to text
			position: absolute;
			top: 10px;
			width: 16px;
			height: 16px;
			margin-bottom: 0;
			margin-left: -30px !important;
			margin-right: 14px !important;
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

input.field__radio,
input.field__checkbox {
	z-index: -1;
	// make sure browser warnings are properly
	// displayed at the correct location
	left: 0px;
	width: 16px;
}

</style>
