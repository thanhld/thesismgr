import * as fs from 'fs-web'
import moment from 'moment'
import { formatMoment } from 'Helper'
import { notify } from 'Components'

export const generateDocx = (input, data, output, actions, call) => {
    const loadFile = (url, callback) => {
        JSZipUtils.getBinaryContent(url, callback)
    }

    loadFile(input, (error, content) => {
        if (error) throw error

        let zip = new JSZip(content)
        let doc = new Docxtemplater().loadZip(zip)

        doc.setData(data)

        try {
            doc.render()
        } catch (error) {
            const e = {
                message: error.message,
                name: error.name,
                stack: error.stack
            }
            console.log(JSON.stringify({error: e}));
            throw error
        }

        let out = doc.getZip().generate({
            type: 'blob',
            mineType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        })

        if (actions) {
            let formData = new FormData()
            formData.append('uploadFile', out, output)
            actions.uploadFile(formData).then(response => {
                const filename = output.split('.')[0]
                const url = response.action.payload.data.url
                actions.createDocument(filename, filename, url, formatMoment(moment())).then(res => {
                    call(res.action.payload.data.documentId)
                }).catch(err => {
                    console.log(err);
                    notify.show(`Không upload được văn bản`, 'danger')
                })
            }).catch(err => {
                notify.show(`Không upload được tệp tin`, 'danger')
            })
        }

        saveAs(out, output)
    })
}
