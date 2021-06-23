<template>
  <button id="generatePdfButton" @click="downloadPdf()">
    Download PDF
  </button>
</template>

<script>
import { jsPDF } from "jspdf";

import "jspdf-autotable";
import itemsHelper from "../mixins/itemsHelpers";

export default {
  name: "DownloadPdf",

  mixins: [itemsHelper],

  data() {
    return {
      currentIndex: 0,
      itemsForPdf: {}
    };
  },
  computed: {
    user() {
      return this.$store.state.user;
    },
    reportData() {
      return this.$store.state.report;
    }
  },
  watch: {},
  methods: {
    getItemsFlatList(items) {
      let index = 0;
      for (let object of items) {
        if (object.items && object.items.length) {
          this.getItemsFlatList(object.items);
          // if (key) {
          // } else {
          //   this.getItemsFlatList(object.items, index.toString());
          // }

          if (object.type === "column") {
            this.currentIndex++;
          }
          continue;
        }
        if (!this.itemsForPdf[this.currentIndex]) {
          this.itemsForPdf[this.currentIndex] = [];
        }
        //
        // if (key) {
        //   if (!this.itemsForPdf[key]) {
        //     this.itemsForPdf[key] = [];
        //   }
        //   this.itemsForPdf[key].push(object);
        // } else {
        //   if (!this.itemsForPdf[index.toString()]) {
        //     this.itemsForPdf[index.toString()] = [];
        //   }
        // }
        this.itemsForPdf[this.currentIndex].push(object);
        index++;
      }
    },

    async downloadPdf() {
      // var doc = new jsPDF("portrait");
      // await this.$store.dispatch("enableExport");
      //
      // setTimeout(async () => {
      //   this.itemsForPdf = [];
      //   this.currentIndex = 0;
      //   this.getItemsFlatList(this.reportData.items);
      //   console.log(this.itemsForPdf);
      //
      //   for (let itemForPdf of this.itemsForPdf) {
      //     let body = [[], []];
      //
      //     let images = {};
      //     let colIndex = 0;
      //     let colIndexesToChange = [];
      //     for (let item of itemForPdf) {
      //       body[0].push(item.label);
      //       if (item.type == "total_score" || item.type == "part_score") {
      //         let className = "#part-score-" + item.reportItemKey;
      //         if (item.type == "total_score") {
      //           className = "#part-score-" + item.reportItemKey;
      //         }
      //
      //         var canvas = document.querySelectorAll(className + " canvas")[0];
      //
      //         if (canvas) {
      //           var dataURL = canvas.toDataURL();
      //
      //           body[1].push("");
      //           colIndexesToChange.push(colIndex);
      //           images[colIndex] = {
      //             imageData: dataURL,
      //             width: canvas.width,
      //             height: canvas.height
      //           };
      //         } else {
      //           body[1].push(this.getReportValue(item));
      //         }
      //       } else {
      //         body[1].push(this.getReportValue(item));
      //       }
      //       colIndex++;
      //     }
      //
      //     const cellWidth = Math.floor(190 / itemForPdf.length);
      //
      //     doc.autoTable({
      //       head: [],
      //       body,
      //       margin: 10,
      //       theme: "plain",
      //       columnStyles: {
      //         text: { cellWidth: cellWidth }
      //       },
      //       didDrawCell: function(data) {
      //         let index = colIndexesToChange.findIndex(
      //           colIndexeToChange => colIndexeToChange === data.column.index
      //         );
      //         if (index >= 0 && data.row.section === "body") {
      //           colIndexesToChange.splice(index, 1);
      //
      //           data.row.height = cellWidth * 0.6;
      //
      //           doc.addImage(
      //             images[data.column.index].imageData,
      //             "JPEG",
      //             data.cell.x + 2,
      //             data.cell.y + 2,
      //             cellWidth,
      //             cellWidth * 0.6
      //           );
      //         }
      //       }
      //     });
      //   }
      //   doc.setFillColor("#000000");
      //   doc.setLineWidth(2);
      //   doc.text("ddddd", 200, 20);
      //   return doc.save();
      // }, 2000);
      // return;
      await this.$store.dispatch("enableExport");

      setTimeout(async () => {
        let element = document.getElementById("div-for-export");
        await html2pdf()
          .set({
            margin: [10, 10],
            pagebreak: { mode: "avoid-all", after: ".page" },
            image: { type: "jpeg", quality: 1 }
          })
          .from(element)
          .save("report.pdf");

        await this.$store.dispatch("disableExport");
      }, 2000);
    }
  }
};
</script>
