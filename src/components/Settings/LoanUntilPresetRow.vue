<template>
	<tr>
		<td>
			<NcTextField :value="preset.name"
				:error="preset.name?.length < 1"
				:label-visible="true"
				:helper-text="preset.name?.length > 1 ? '' : t('biblio', 'Invalid name')"
				:label="t('biblio', 'Name')"
				@update:value="changeName" />
		</td>
		<td>{{ preset.type }}</td>
		<td>{{ preset.timestamp }}</td>
	</tr>
</template>
<script>
import { mapStores } from "pinia";
import debounceFn from "debounce-fn";

import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";

import { useSettingsStore } from "../../store/settings.js";
import { api } from "../../api.js";

export default {
	components: {
		NcTextField,
	},
	props: {
		preset: {
			type: Object,
			default: () => ({}),
		},
	},
	computed: {
		...mapStores(useSettingsStore),
	},
	methods: {
		changeName: debounceFn(function(newName) {
			api.updateLoanUntilPreset(this.settingsStore.context?.collectionId, this.preset.id, {
				name: newName,
			}).then((result) => {
				this.$emit("update:preset", result);
			});
		}, { wait: 100 }),
	},
};
</script>
