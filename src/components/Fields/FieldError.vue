<template>
	<NcNoteCard type="error" :heading="t('biblio', 'Error')">
		<p>{{ t("biblio", "This field has an invalid value, to reset it click the button below (will only affect this item)") }}</p>
		<NcButton :aria-label="t('biblio', 'Reset field')"
			type="error"
			@click="$emit('reset')">
			<template #icon>
				<Undo :size="20" />
			</template>
			{{ t("biblio", "Reset") }}
		</NcButton>
	</NcNoteCard>
</template>
<script>
import NcNoteCard from "@nextcloud/vue/dist/Components/NcNoteCard.js";
import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import Undo from "vue-material-design-icons/Undo";

export default {
	components: {
		NcNoteCard,
		NcButton,
		Undo,
	},
};
</script>
