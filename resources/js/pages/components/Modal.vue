<template>
    <div class="modal fade" :id="id" tabindex="-1" :data-bs-backdrop="backdrop" aria-labelledby="modalComponentIdLabel">
        <div class="overlay" style="position: absolute;
            width: 100%;
            height: 100%;
            /* z-index: 0; */
            background-color: #000;
            opacity: .5;"
        v-if="overlay == true">

        </div>
        <div class="modal-dialog" :class="modalSize" :style="styleSize">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="modalComponentIdLabel"><icons icon="fas fa-info-circle fa-sm"></icons> {{ title }}</h4>
                    <!-- <h4 class="modal-title" :id="areaLabelledBy"><icons :icon="modalIcon"></icons> {{ title }}</h4> -->
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <slot name="body" />
                </div>
                <div class="modal-footer justify-content-between" v-if="modalFooter == true">
                    <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">
                        Close
                    </button>
                    <slot name="footerButton"></slot>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

    defineProps({
        title: {
            type: String,
            default: "<<Title goes here>>",
        },
        backdrop: {
            type: String,
            default: 'true',
        },
        modalSize: {
            type: String,
            default: '',
        },
        styleSize: {
            type: String,
            default: ''
        },
        id: {
            type: String,
            default: ''
        },
        modalIcon: {
            type : String,
            default: 'fas fa-info-circle fa-sm'
        },
        modalFooter: {
            type: Boolean,
            default: true
        },
        overlay: {
            type: Boolean,
            default: false
        }
    });
</script>