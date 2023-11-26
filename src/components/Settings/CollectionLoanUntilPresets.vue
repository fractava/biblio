<template>
	<div>
		<table class="loanUntilPresetsTable">
			<colgroup>
				<col span="3">
				<col style="width: fit-content;">
			</colgroup>
			<thead>
				<tr>
					<th>{{ t("biblio", "Name") }}</th>
					<th>{{ t("biblio", "Type") }}</th>
					<th>{{ t("biblio", "Time") }}</th>
					<th>{{ t("biblio", "Actions") }}</th>
				</tr>
			</thead>
			<tbody>
				<LoanUntilPresetRow v-for="(preset, index) in presets"
					:key="preset.id"
					:preset.sync="presets[index]"
					@refresh="fetchPresets" />
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

import Plus from "vue-material-design-icons/Plus.vue";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";
import LoanUntilPresetRow from "./LoanUntilPresetRow.vue";

export default {
	components: {
		NcActions,
		NcActionButton,
		Plus,
		LoanUntilPresetRow,
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
		this.fetchPresets();
	},
	methods: {
		fetchPresets() {
			api.getLoanUntilPresets(this.settingsStore.context?.collectionId).then((result) => {
				this.presets = result;
			});
		},
		addPreset() {
			api.createLoanUntilPreset(this.settingsStore.context?.collectionId, {
				name: "Test 1",
				type: "absolute",
				timestamp: Math.floor(Date.now() / 1000),
			}).then((result) => {
				this.fetchPresets();
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
