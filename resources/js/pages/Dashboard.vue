<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4">
                    <div class="small-box bg-gradient-warning mt-3">
                        <div class="inner">
                            <h3>{{ dashboardStat.open }}</h3>
                            <h5>Open RFQ</h5>
                        </div>
                        <div class="icon">
                            <!-- <i class="fas fa-user-plus"></i> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="small-box bg-gradient-info mt-3">
                        <div class="inner">
                            <h3>{{ dashboardStat.forQuot }}</h3>
                            <h5>For Quotation</h5>
                        </div>
                        <div class="icon">
                            <!-- <i class="fas fa-user-plus"></i> -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="small-box bg-gradient-success mt-3">
                        <div class="inner">
                            <h3>{{ dashboardStat.forHeadAp }}</h3>
                            <h5>For Head Approval</h5>
                        </div>
                        <div class="icon">
                            <!-- <i class="fas fa-user-plus"></i> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import api from '../axios';

    const dashboardStat = reactive({
        open: 0,
        forQuot: 0,
        forHeadAp: 0
    });

    onMounted(() => {
        api.get('api/get_stat').then((result)=>{
            dashboardStat.open = result.data.open;
            dashboardStat.forQuot = result.data.for_quotation;
            dashboardStat.forHeadAp = result.data.for_approval;
        }).catch((err) => {
            console.log(err);
        });
    })
</script>