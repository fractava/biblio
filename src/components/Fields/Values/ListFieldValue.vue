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
	<div class="field__content">
		<div v-if="valueError">
			<FieldError @reset="resetValue" />
		</div>
		<div v-else>
			<div v-if="allowValueEdit">
				<div v-for="(entry, index) in validatedValue"
					:key="index"
					style="display: flex; align-items: center;">
					<NcTextField class="entry"
						:value="entry"
						:show-trailing-button="false"
						:label="t('biblio', 'List Entry')" />
					<NcActions>
						<NcActionButton @click="deleteEntry(index)">
							<template #icon>
								<Delete :size="20" />
							</template>
							{{ t('biblio', 'Delete Entry') }}
						</NcActionButton>
					</NcActions>
				</div>
				<NcTextField class="addNewEntry"
					:value.sync="newEntryValue"
					:show-trailing-button="!isNewEntryEmpty"
					:label="t('biblio', 'Add Entry')"
					trailing-button-icon="arrowRight"
					@trailing-button-click="addEntry"
					@keydown.enter.prevent="addEntry">
					<Plus :size="20" />
				</NcTextField>
			</div>
			<div v-else>
				<ul style="list-style: circle; list-style-position: inside;">
					<li v-for="(entry, index) in validatedValue"
						:key="index">
						{{ entry }}
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<script>
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";

import FieldError from "../FieldError.vue";

import FieldValue from "../../mixins/FieldValue.js";

import Plus from "vue-material-design-icons/Plus.vue";
import Delete from "vue-material-design-icons/Delete.vue";

export default {
	components: {
		NcTextField,
		NcActions,
		NcActionButton,
		FieldError,
		Plus,
		Delete,
	},
	mixins: [FieldValue],
	props: {
		value: {
			default: () => ([]),
		},
	},
	data() {
		return {
			defaultValue: [],
			newEntryValue: "",
		};
	},
	computed: {
		isNewEntryEmpty() {
			return this.newEntryValue?.trim().length === 0;
		},
		hasNoEntry() {
			return this.value.length === 0;
		},
		valueError() {
			return !Array.isArray(this.value);
		},
		validatedValue() {
			if (Array.isArray(this.value)) {
				return this.value;
			} else {
				return this.defaultValue;
			}
		},
	},
	watch: {
	},
	methods: {
		addEntry() {
			if (!this.isNewEntryEmpty) {
				this.$emit("update:value", [...this.value, this.newEntryValue]);
				this.newEntryValue = "";
			}
		},
		deleteEntry(index) {
			const clonedValue = this.value.slice();
			clonedValue.splice(index, 1);
			this.$emit("update:value", clonedValue);
		},
		resetValue() {
			this.$emit("update:value", this.defaultValue);
		},
	},
};
</script>

<style lang="scss" scoped>
.field__content {
	display: flex;
	flex-direction: column;
}
</style>
