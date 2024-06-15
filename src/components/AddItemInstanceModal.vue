<template>
	<div>
		<NcModal v-if="open"
			:name="t('biblio', 'Add new Item Instance')"
			@close="closeModal">
			<div class="modal__content">
				<h2>{{ t('biblio', 'Add new Item Instance') }}</h2>

				<ButtonGroup style="margin-bottom: 10px;">
					<NcButton :pressed="!multipleMode"
						@click="multipleMode = false">
						{{ t('biblio', 'Single Mode') }}
					</NcButton>
					<NcButton :pressed="multipleMode"
						@click="multipleMode = true">
						{{ t('biblio', 'Batch Mode') }}
					</NcButton>
				</ButtonGroup>

				<div v-if="!multipleMode">
					<NcTextField :label="t('biblio', 'Barcode')" :value.sync="barcode" />
				</div>
				<div v-else>
					<NcTextField :label="t('biblio', 'Prefix')" :value.sync="prefix" />
					<NcInputField :label="t('biblio', 'Start Number')" type="number" :value.sync="startNumber" />
					<NcInputField :label="t('biblio', 'Count')" type="number" :value.sync="count" />
					<NcTextField :label="t('biblio', 'Suffix')" :value.sync="suffix" />
					<NcNoteCard v-if="startNumber > 0 && count > 0" type="info">
						<span>{{ actionDescription }}</span>
					</NcNoteCard>
					<NcNoteCard :type="warningCardType">
						<span>{{ existingWarning }}</span>
					</NcNoteCard>
				</div>

				<NcButton :disabled="submitDisabled"
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
import debounceFn from "debounce-fn";

import NcButton from "@nextcloud/vue/dist/Components/NcButton.js";
import NcModal from "@nextcloud/vue/dist/Components/NcModal.js";
import NcTextField from "@nextcloud/vue/dist/Components/NcTextField.js";
import NcInputField from "@nextcloud/vue/dist/Components/NcInputField.js"
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon.js";
import NcNoteCard from '@nextcloud/vue/dist/Components/NcNoteCard.js'

import Plus from "vue-material-design-icons/Plus.vue";

import ButtonGroup from "./ButtonGroup.vue";
import { useBiblioStore } from "../store/biblio.js";
import { api } from "../api.js";

export default {
	components: {
		NcButton,
		NcModal,
		NcTextField,
		NcInputField,
		NcLoadingIcon,
		NcNoteCard,
		Plus,
		ButtonGroup,
	},
	props: {
		open: {
			type: Boolean,
			default: false,
		},
		itemId: {
			type: Number,
			required: true,
		},
		defaultPrefix: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			multipleMode: false,
			loading: false,
			barcode: "",
			prefix: "",
			startNumber: 1,
			count: 1,
			suffix: "",
			existingBarcodes: [],
			anyNewBarcodes: true,
		};
	},
	computed: {
		...mapStores(useBiblioStore),
		submitDisabled() {
			if(this.multipleMode) {
				return !(this.startNumber && (this.count > 0)  && this.anyNewBarcodes);
			} else {
				return !(this.barcode);
			}
		},
		actionDescription() {
			if(this.multipleMode) {
				if(this.count == 1) {
					return t('biblio', 'Press submit to create barcode {barcode}', { barcode: (this.prefix + this.startNumber + this.suffix) });
				} else if(this.count <= 3) {
					let barcodes = [];

					for(let i = 0; i < this.count; i++) {
						barcodes.push(this.prefix + (this.startNumber + i) + this.suffix);
					}

					return t('biblio', 'Press submit to create barcodes {firstBarcodesList} and {lastBarcode}', {
						firstBarcodesList: barcodes.slice(0, -1).join(", "),
						lastBarcode: barcodes.at(-1)
					});
				} else {
					return t('biblio', 'Press submit to create barcodes {firstBarcode}, {secondBarcode}, ... {lastBarcode}', {
						firstBarcode: (this.prefix + this.startNumber + this.suffix),
						secondBarcode: (this.prefix + (this.startNumber + 1) + this.suffix),
						lastBarcode: (this.prefix + (this.startNumber + this.count - 1) + this.suffix)
					});
				}
			} else {
				return "";
			}
		},
		warningCardType() {
			if(this.multipleMode) {
				if(this.startNumber > 0 && this.count > 0 && this.anyNewBarcodes) {
					if(this.existingBarcodes.length > 0) {
						return "warning";
					} else {
						return "success";
					}
				} else {
					return "error";
				}
			}
		},
		existingWarning() {
			if(this.multipleMode) {
				if(this.startNumber > 0 && this.count > 0) {
					if(this.anyNewBarcodes) {
						if(this.existingBarcodes.length === 0) {
							return t('biblio', 'No barcodes in the selected range already exist');
						} else if(this.existingBarcodes.length == 1) {
							return t('biblio', 'Barcode {barcode} already exist and will not be created', { barcode: this.existingBarcodes[0] });
						} else if(this.existingBarcodes.length <= 4) {
							return t('biblio', 'Barcodes {barcodeList} already exist and will not be created', { barcodeList: this.existingBarcodes.join(", ") });
						} else {
							return t('biblio', 'Barcodes {barcodeList} and {additionalNumber} more already exist and will not be created', {
								barcodeList: this.existingBarcodes.slice(0, 4).join(", "),
								additionalNumber: this.existingBarcodes.length - 4,
							});
						}
					} else {
						return t('biblio', 'All included barcodes already exist');
					}
				} else {
					return t('biblio', 'You need to select a valid range');
				}
			} else {
				return "";
			}
		}
	},
	watch: {
		defaultPrefix() {
			this.resetBarcode();
		},
		prefix() {
			this.reloadTest();
		},
		startNumber() {
			this.reloadTest();
		},
		count() {
			this.reloadTest();
		},
		suffix() {
			this.reloadTest();
		},
		open() {
			this.resetInputs();
			this.reloadTest();
		}
	},
	mounted() {
		this.resetInputs();
		this.reloadTest();
	},
	methods: {
		closeModal() {
			this.$emit("update:open", false);
			this.resetBarcode();
		},
		resetBarcode() {
			this.barcode = this.defaultPrefix;
			this.prefix = this.defaultPrefix;
		},
		resetInputs() {
			this.resetBarcode();
			this.startNumber = 1;
			this.count = 1;
			this.suffix = "";
		},
		reloadTest: debounceFn(async function () {
			if(this.startNumber > 0 && this.count > 0) {
				({existingBarcodes: this.existingBarcodes, anyNewBarcodes: this.anyNewBarcodes } = await api.batchCreateItemInstanceTest(this.$route.params.collectionId, {
					itemId: this.itemId,
					prefix: this.prefix,
					startNumber: this.startNumber,
					count: this.count,
					suffix: this.suffix,
				}));
			} else {
				this.existingBarcodes = [];
				this.anyNewBarcodes = false;
			}
		}, { wait: 200 }),
		async submit() {
			this.loading = true;
			try {
				if(this.multipleMode) {
					const newInstances = await api.batchCreateItemInstance(this.$route.params.collectionId, {
						itemId: this.itemId,
						prefix: this.prefix,
						startNumber: this.startNumber,
						count: this.count,
						suffix: this.suffix,
					});

					this.$emit("added-instances", newInstances);
				} else {
					const newInstance = await api.createItemInstance(this.$route.params.collectionId, {
						itemId: this.itemId,
						barcode: this.barcode,
					});

					this.$emit("added-instances", [newInstance]);
				}

			} catch (error) {
				console.error(error);
			} finally {
				this.loading = false;
				this.closeModal();
				this.resetBarcode();
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
