<template>
  <div>
    <button v-if="!exportEnabled" id="generatePdfButton" @click="downloadPdf()">
      Download PDF
    </button>
  </div>
</template>

<script>
import { jsPDF } from "jspdf";

import Api from "../Api";
import "jspdf-autotable";
import itemsHelper from "../mixins/itemsHelpers";
const downloadFile = (blob, fileName) => {
  const link = document.createElement("a");
  // create a blobURI pointing to our Blob
  link.href = URL.createObjectURL(blob);
  link.download = fileName;
  // some browser needs the anchor to be in the doc
  document.body.append(link);
  link.click();
  link.remove();
  // in case the Blob uses a lot of memory
  setTimeout(() => URL.revokeObjectURL(link.href), 7000);
};
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
    },
    exportEnabled() {
      return this.$store.state.exportEnabled;
    }
  },
  watch: {},
  methods: {
    getItemsFlatList(items) {
      let index = 0;
      for (let object of items) {
        if (object.component === "LOCItems") {
          continue;
        }
        if (object.items && object.items.length) {
          this.getItemsFlatList(object.items);

          if (object.type === "column") {
            this.currentIndex++;
          }
          continue;
        }
        if (!this.itemsForPdf[this.currentIndex]) {
          this.itemsForPdf[this.currentIndex] = [];
        }

        this.itemsForPdf[this.currentIndex].push(object);
        index++;
      }
    },

    async downloadPdf() {
      var doc = new jsPDF("portrait");
      await this.$store.dispatch("enableExport");
      this.$store.dispatch("updateDownloadPercentage", 0);
      setTimeout(async () => {
        let currentPage = 0;
        for (const page of this.getItems(this.reportData.items)) {
          this.itemsForPdf = [];
          this.currentIndex = 0;

          this.getItemsFlatList(page.items);

          let percentage = Math.round(
            (currentPage / this.reportData.items.length) * 100
          );

          this.$store.dispatch("updateDownloadPercentage", percentage);
          currentPage++;
          for (let itemForPdf of this.itemsForPdf) {
            let body = [[], []];

            let images = {};
            let colIndex = 0;
            let colImageIndexesToChange = [];
            for (let item of itemForPdf) {
              if (item.newPageInPdf) {
                doc.addPage();
              }
              if (!["paragraph"].includes(item.type)) {
                body[0].push({
                  content: this.getLabel(item),
                  styles: { fontStyle: "bold" }
                });
              }
              if (
                ["radio_grid", "total_score", "part_score"].includes(item.type)
              ) {
                let className = "#part-score-" + item.reportItemKey;
                if (item.type == "total_score") {
                  className = "#total-score-" + item.reportItemKey;
                }
                if (item.type == "radio_grid") {
                  className = "#radio-grid-" + item.reportItemKey;
                }

                var canvas = document.querySelectorAll(
                  className + " canvas"
                )[0];

                images[colIndex] = {
                  data: this.getRadioGridData(item)
                };
                body[1].push("");
                colImageIndexesToChange.push(colIndex);
                if (canvas) {
                  var dataURL = canvas.toDataURL();

                  images[colIndex].imageData = dataURL;
                  images[colIndex].width = canvas.width;
                  images[colIndex].height = canvas.height;
                } else {
                  body[1].push(this.getReportValue(item));
                }
              } else if (["paragraph"].includes(item.type)) {
                const str = item.label;

                const splitString = str.match(/<p(.*?)>.*?<\/p>/g);

                let bodyIndex = 0;
                if (splitString) {
                  for (const splitStringItem of splitString) {
                    const div = document.createElement("div");
                    div.innerHTML = splitStringItem;

                    const content = div.textContent || div.innerText || "";
                    const styles = {};
                    if (splitStringItem.includes("<strong>")) {
                      styles.fontStyle = "bold";
                    }
                    if (splitStringItem.includes("text-align: center;")) {
                      styles.halign = "center";
                    }

                    if (!body[bodyIndex]) {
                      body[bodyIndex] = [];
                    }
                    body[bodyIndex].push({
                      content,
                      styles
                    });
                    bodyIndex++;
                  }
                }
              } else if (["file_upload"].includes(item.type)) {
                const value = this.getReportValue(item);
                if (value) {
                  let lineIndex = 1;
                  for (let valueItem of value) {
                    await Api.get(aoat_config.aoatGetMediaUrl + valueItem)
                      .then(response => {
                        body[lineIndex].push(response.data.source_url);
                      })
                      .catch(() => {});
                    lineIndex++;
                  }
                }
              } else {
                body[1].push(this.getReportValue(item));
              }
              colIndex++;
            }
            const cellWidth = Math.floor(190 / itemForPdf.length);
            const $this = this;

            doc.autoTable({
              head: [],
              body,
              margin: 10,
              theme: "plain",
              tableWidth: 190,
              styles: {
                cellWidth: cellWidth
              },
              willDrawCell: function(data) {},
              didDrawCell: function(data) {
                let index = colImageIndexesToChange.findIndex(
                  colIndexeToChange => colIndexeToChange === data.column.index
                );
                if (index >= 0 && data.row.section === "body") {
                  colImageIndexesToChange.splice(index, 1);
                  $this.drawTableAndImage(doc, data, images, cellWidth);
                }
              }
            });
          }
          doc.addPage();
        }
        doc.setFillColor("#000000");
        doc.setLineWidth(2);

        await this.$store.dispatch("disableExport");
        return doc.save();
      }, 2000);
    },
    drawTableAndImage(doc, data, images, cellWidth) {
      let height1 = 0;
      let currentRowIndex = -1;
      // if (data.cell.y > 100) {
      //   doc.addPage();
      //   data.cell.y = 10;
      // }
      doc.autoTable({
        startY: data.cell.y + 10,
        tableWidth: data.cell.width - 10,
        theme: "grid",
        headStyles: { fillColor: 255, textColor: 0, lineWidth: 0.1 },
        columns: images[data.column.index].data.columns,
        body: images[data.column.index].data.body,
        willDrawCell: function(hookData) {
          if (hookData.row.index === 0 && hookData.section === "head") {
            doc.setTextColor(
              images[data.column.index].data.colors[hookData.column.index]
            );
          }
          if (hookData.row.index !== currentRowIndex) {
            height1 += hookData.row.height;
            currentRowIndex = hookData.row.index;
          }
        }
      });

      if (images[data.column.index].imageData) {
        const cellHeight =
          cellWidth *
          (images[data.column.index].height / images[data.column.index].width);

        doc.addImage(
          images[data.column.index].imageData,
          "JPEG",
          data.cell.x,
          data.cell.y + height1 + 30,
          cellWidth,
          cellHeight,
          null,
          "FAST"
        );
        data.row.height = cellHeight + height1 + 30;
      } else {
        data.row.height = height1 + 15;
      }
    },
    getRadioGridData(object) {
      const value = this.getReportValue(object);
      const data = {
        colors: [],
        columns: [],
        body: []
      };

      data.columns.push({ dataKey: "id", header: "" });
      data.colors.push("#000000");
      if (!object.optionsVertical) {
        console.log(object);
      }
      data.columns = data.columns.concat(
        object.optionsVertical.map(optionVertical => {
          data.colors.push(optionVertical.color);
          return {
            dataKey: optionVertical.id + "_",
            header: optionVertical.name
          };
        })
      );

      data.colors.push("#000000");
      data.columns.push({ dataKey: "end", header: "Score" });

      for (const optionHorizontal of object.optionsHorizontal) {
        const bodyItem = {
          id: optionHorizontal.name
        };
        const selectedVerticalOption = object.optionsVertical.find(
          optionVertical => optionVertical.id === value[optionHorizontal.id]
        );
        bodyItem.end = 0;
        if (selectedVerticalOption) {
          bodyItem.end = selectedVerticalOption.score ?? 0;
        } else {
          console.log(object);
          console.log(value);
          console.log(optionHorizontal);
        }

        for (const optionVertical of object.optionsVertical) {
          const text = "";
          if (value[optionHorizontal.id] === optionVertical.id) {
            text = 1;
          }
          bodyItem[optionVertical.id + "_"] = text;
        }

        data.body.push(bodyItem);
      }

      return data;
    },
    getLabel(item) {
      if (item.reportLabel && item.reportLabel !== "") {
        return item.reportLabel;
      }
      return item.label;
    }
  }
};
</script>
