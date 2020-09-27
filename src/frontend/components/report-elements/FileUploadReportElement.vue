<template>
  <div>
    <h5>{{ object.label }}</h5>
    <template v-if="media">
      <a :href="media.source_url" target="_blank">{{ media.title.rendered }}</a>
    </template>

    </div>
</template>

<script>
import axios from 'axios'

  export default {

    name: 'TextReportElement',

    components: {

    },

    props: {
      object: {
        type: Object,
        required: true,
      }
    },

    computed: {

    },

    data () {
      return {
        media: null
      };
    },

    mounted() {
      this.getMedia()
    },

    methods: {
      getMedia() {
        axios.get( aoat_config.aoatGetMediaUrl  + this.$store.state.assessment[this.object.reportItemKey]).then(response => {
          this.media = response.data
          console.log(this.media);
        })
      }
    }

  };
</script>
