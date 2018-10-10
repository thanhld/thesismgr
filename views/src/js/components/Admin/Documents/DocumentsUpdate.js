import React, { Component } from 'react'
import moment from 'moment'
import DatePicker from 'react-datepicker'
import { Modal, notify } from 'Components'
import { formatMoment } from 'Helper'

class DocumentsUpdate extends Component {
    constructor(props) {
        super(props)
        this.state = {
            cur_document: {
                id: '',
                attachment: { url: '' },
                documentCode: '',
                createdDate: moment()
            },
            editFile: false
        }
    }
    componentWillReceiveProps(nextProps) {
        const { id, documentCode, createdDate, attachment } = nextProps.document
        this.setState({
            cur_document: {
                id,
                attachment: attachment || {url: ''},
                documentCode,
                createdDate: moment(createdDate)
            }
        })
    }
    handleDateChange = date => {
        this.setState({
            cur_document: {
                ...this.state.cur_document,
                createdDate: date
            }
        })
    }
    submitFile = callback => {
        const { actions } = this.props
        let files = document.getElementById('uploadFile').files
        let formData = new FormData()
        formData.append('uploadFile', files[0], files[0].name)
        actions.uploadFile(formData).then(response => {
            callback(response.action.payload.data.url)
        }).catch(err => {
            this.setState({
                error: true,
                message: 'Không upload được file'
            })
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { actions, modalId } = this.props
        const { cur_document, editFile } = this.state
        const { id, documentCode, createdDate } = cur_document
        const editDocument = fileUrl => {
            let data = {}
            data['documentCode'] = documentCode
            data['name'] = documentCode
            data['createdDate'] = formatMoment(createdDate)
            if (fileUrl) data['url'] = fileUrl
            actions.editDocument(id, data).then(() => {
                actions.loadDocuments()
                $(`#${modalId}`).modal('hide')
                notify.show('Cập nhật quyết định thành công', 'primary')
            }).catch(err => {
                notify.show(err.response.data.message, 'danger')
            })
        }
        let files = editFile && document.getElementById('uploadFile').files
        if (editFile && files && files.length > 0) {
            this.submitFile(fileUrl => {
                editDocument(fileUrl)
            })
        } else editDocument()
    }
    render() {
        const { modalId, onSubmit, document } = this.props
        const { cur_document, editFile } = this.state
        return <Modal
            modalId={modalId}
            title={`Chỉnh sửa tờ trình, quyết định`}
            onSubmit={this.handleSubmit}
            >
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Số (*)</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value={cur_document.documentCode || ''} onChange={e => this.setState({cur_document: {...cur_document, documentCode: e.target.value}})} required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Ngày (*)</label>
                <div class="col-sm-8">
                    <DatePicker
                        className="form-control"
                        selected={cur_document.createdDate}
                        onChange={this.handleDateChange}
                        required
                    />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-offset-1 col-sm-2 control-label">Toàn văn</label>
                <div class="col-sm-7">
                    { cur_document.attachment.url ? <a class="form-control" href={cur_document.attachment.url}>
                        {document.documentCode}
                    </a> : <p class="form-control">
                        Chưa có toàn văn
                    </p> }
                </div>
                { !editFile && <i class="col-sm-1 fa fa-edit clickable" title="Nhấn để sửa tệp toàn văn" onClick={e => this.setState({editFile: true})}></i> }
                { editFile && <i class="col-sm-1 fa fa-undo text-danger clickable" title="Nhấn để bỏ qua sửa" onClick={e => this.setState({editFile: false})}></i> }
            </div>
            { editFile && <div class="form-group">
                <div class="col-sm-offset-3 col-sm-8">
                    <input id="uploadFile" type="file" class="form-control" />
                </div>
            </div> }
        </Modal>
    }
}

export default DocumentsUpdate
