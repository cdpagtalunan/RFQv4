<template>
    <section class="content">
        <div class="container-fluid">
            <div style="margin-top: 8%;">
                <h4 class="text-center">Request for Quotation v4</h4>

                <hr class="my-4 ">

                <h1 class="display-2 text-center">DO NOT CLOSE</h1>
                
                <hr class="my-4 ">
                
                <p class="text-center">Auto Mail Notification</p>
                <!-- <p class="text-center">{{ date }}</p> -->
                <p class="text-center">{{ timer }}</p>
                <!-- <p class="text-center">{{ day }}</p> -->
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import moment from 'moment'
    import api from '../axios';

    const timer = ref();
    onMounted(() => {
        setInterval(getClock, 1000);
    });

    const getClock = () => {
        timer.value = moment().format('LTS');
        if(timer.value == '7:00:00 AM'){
            api.get('/get_pending_requests').then((result)=>{
                // Script Here
            }).catch((err) => {
                console.log(err);
            });
        }
    }
</script>