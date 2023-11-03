<template>
	<div class="editModeContainer">
		<NcButton :aria-label="editMode ? t('biblio', 'Exit editing mode') : t('biblio', 'Enter editing mode')"
			@click="setEditMode(!editMode)">
			<template #icon>
				<Pencil :size="20" />
			</template>
			{{ t("biblio", "Edit") }}
		</NcButton>
	</div>
</template>
<script>
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";

import Pencil from "vue-material-design-icons/Pencil.vue";

export default {
	components: {
		NcButton,
		Pencil,
	},
	props: {
		editMode: {
			type: Boolean,
			default: false,
		},
	},
	methods: {
		setEditMode(value) {
			this.$emit("update:editMode", value);
		},
	},
};
</script>
<style scoped>
.editModeContainer {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 20px;
}
</style>
