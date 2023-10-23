<template>
	<div>
		<NcButton icon="add"
			type="secondary"
			@click="showModal">
			{{ t('biblio', 'New Instance') }}
			<template #icon>
				<Plus :size="20" />
			</template>
		</NcButton>
		<NcModal v-if="modalOpen"
			:name="t('biblio', 'Add new Item Instance')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new Item Instance') }}</h2>

				<NcTextField label="Barcode" :value.sync="barcode" />

				<NcButton :disabled="!barcode"
					type="primary"
					style="margin-top: 15px;"
					@click="submit">
					Submit
					<template #icon>
						<Plus v-if="!loading" :size="20" />
						<NcLoadingIcon v-else />
					</template>
				</NcButton>
			</div>
		</NcModal>
	</div>
</template>

<script>
import { mapStores } from "pinia";

import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js';

import Plus from "vue-material-design-icons/Plus.vue";

import { useBiblioStore } from "../store/biblio.js";
import { api } from "../api.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcLoadingIcon,
		Plus,
	},
	props: {
		itemId: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			loading: false,
			modalOpen: false,
			barcode: "",
		};
	},
	computed: {
		...mapStores(useBiblioStore),
	},
	methods: {
		showModal() {
			this.modalOpen = true;
		},
		closeModal() {
			this.modalOpen = false;
		},
		async submit() {
			this.loading = true;
			try {
				let newInstance = await api.createItemInstance(this.$route.params.collectionId, {
					itemId: this.itemId,
					barcode: this.barcode,
				});

				this.$emit("added-instance", newInstance);
			} catch (error) {
				console.error(error);
			} finally {
				this.loading = false;
				this.closeModal();
				this.barcode = "";
			}
		},
	},
};
</script>
<style scoped>
.modal__content {
	margin: 50px;
}

.modal__content h2 {
	text-align: center;
}
</style>