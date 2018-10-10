import React, { Component } from 'react'
import { topicTypeToTypeId } from 'Helper'
import { mailerActions } from 'Actions';

class AddLearnersModal extends Component {
    constructor(props) {
        super(props)
        this.state = {
            newInter: '0',
            students: '',
            error: false,
            message: ''
        }
    }
    dismissModal = () => {
        const { modalId } = this.props
        this.setState({
            error: false,
            students: '',
            newInter: '0',
            message: ''
        })
        $(`#${modalId}`).modal('hide')
        $("#newInter option[value='0']").prop('selected', true);
    }
    handleFormChange = e => {
        const name = e.target.name
        const value = e.target.value
        this.setState({
            [name]: value
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { students } = this.state
        const { actions, modalId, reloadData, type } = this.props
        const splitStudents = students.trim().split(/[\n,; ]/)
        let listStudents = []
        splitStudents.forEach(s => {
            if (s) listStudents.push(s)
        })
        actions.importLearnerCodes({data: listStudents, topicType: topicTypeToTypeId(type)}).then(res => {
            const { error } = res.action.payload.data
            if (error.length == 0) {
                mailerActions.registerTopicMail();
                reloadData()
                this.dismissModal()
            } else {
                reloadData()
                this.setState({
                    error: true,
                    newInter: '0',
                    message: error.join(' ,')
                })
            }
        })
    }
    render() {
        const { modalId, title, type } = this.props
        const { students, error, message } = this.state
        return <div id={modalId} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="addLearnerModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick={this.dismissModal}>
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="addLearnerModal">{title}</h4>
                    </div>
                    <form class="form-horizontal" onSubmit={this.handleSubmit}>
                        <div class="modal-body">
                            { error && <div>
                                <div class="text-message-error">
                                    Các mã học viên đăng ký không thành công: { message }
                                </div>
                                <br />
                            </div> }
                            <div class="form-group">
                              {topicTypeToTypeId(type) == 1 && <label class="col-sm-2 control-label"> Danh sách sinh viên đủ điều kiện</label>}
                              {topicTypeToTypeId(type) == 2 && <label class="col-sm-2 control-label"> Danh sách học viên đủ điều kiện</label>}
                                <div class="col-sm-9">
                                    <textarea name="students" class="form-control" rows={5} value={students} placeholder="Danh sách mã sinh viên/học viên được phân cách bởi dấu trắng, dấu phẩy, dấu chấm phẩy hoặc xuống dòng." onChange={this.handleFormChange} />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Đồng ý</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" onClick={this.dismissModal}>Bỏ qua</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    }
}

export default AddLearnersModal
