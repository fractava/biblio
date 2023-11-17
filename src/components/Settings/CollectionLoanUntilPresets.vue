<template>
	<div>
		<table class="loanUntilPresetsTable">
			<thead>
				<tr>
					<th>{{ t("biblio", "Name") }}</th>
					<th>{{ t("biblio", "Type") }}</th>
					<th>{{ t("biblio", "Time") }}</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="preset in presets"
					:key="preset.id">
					<td>
						<NcTextField :value="preset.name"
							:error="preset.name?.length < 1"
							:label-visible="true"
							:helper-text="preset.name?.length > 1 ? '' : t('biblio', 'Invalid name')"
							:label="t('biblio', 'Name')" />
					</td>
					<td>{{ preset.type }}</td>
					<td>{{ preset.timestamp }}</td>
				</tr>
			</tbody>
		</table>
		<NcActions class="addPresetButton"
			primary
			:force-name="true">
			<NcActionButton @click="addPreset">
				{{ t("biblio", "Add Preset") }}
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcActionButton>
		</NcActions>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcActions from "@nextcloud/vue/dist/Components/NcActions.js";
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import Plus from "vue-material-design-icons/Plus.vue";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";

export default {
	components: {
		NcActions,
		NcActionButton,
		NcTextField,
		Plus,
	},
	data() {
		return {
			presets: [],
		};
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	mounted() {
		this.fetchMembers();
	},
	methods: {
		fetchMembers() {
			api.getLoanUntilPresets(this.settingsStore.context?.collectionId).then((result) => {
				this.presets = result;
			});
		},
		addPreset() {
			api.createLoanUntilPreset(this.settingsStore.context?.collectionId, {
				name: "Test 1",
				type: "absolute",
				timestamp: 1000000,
			}).then((result) => {
				console.log(result);
			});
		},
	},
};
</script>
<style lang="scss">
.loanUntilPresetsTable {
	width: 100%;
	border-collapse: collapse;

	th {
		font-weight: bold;
	}

	td, th {
		padding: 7px;
		border-bottom: 1px solid var(--color-border);
	}

	tr:hover, tr:focus, tr:active {
		background-color: transparent;
	}
}
</style>
