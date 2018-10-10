import React, { Component } from 'react'
import { Modal, notify } from 'Components'

class SuperuserQuotasUpdate extends Component {
    constructor(props) {
        super(props)
        this.state = {
            error: false,
            message: '',
            version: '',
            data: [],
            mainFactor: [1, 1, 1],
            coFactor: [1, 1, 1]
        }
    }
    componentWillReceiveProps(nextProps) {
        const { changeQuota } = nextProps
        const { version, data, mainFactor, coFactor } = changeQuota
        this.setState({
            error: false,
            version,
            data,
            mainFactor,
            coFactor
        })
    }
    changeFactor = (idx, type) => e => {
        e.preventDefault()
        const { mainFactor, coFactor } = this.state
        let newData = type == 'mainFactor' ? [...mainFactor] : [...coFactor]
        newData[idx] = e.target.value
        this.setState({
            [type]: newData
        })
    }
    handleChangeData = (idx, type) => e => {
        e.preventDefault()
        const field = type == 1 ? 'maxStudent' : type == 2 ? 'maxGraduated' : 'maxResearcher'
        let newData = [...this.state.data]
        newData[idx][field] = e.target.value
        this.setState({
            data: newData
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { actions, reloadData } = this.props
        const { version, data, mainFactor, coFactor } = this.state
        let subData = [...data]
        subData.forEach(d => delete d['degreeId'])
        let submitData = {
            mainFactorStudent: mainFactor[0],
            mainFactorGraduated: mainFactor[1],
            mainFactorResearcher: mainFactor[2],
            coFactorStudent: coFactor[0],
            coFactorGraduated: coFactor[1],
            coFactorResearcher: coFactor[2],
            data: subData
        }
        actions.updateQuota(version, submitData).then(res => {
            reloadData(() => {
                $('#updateQuotas').modal('hide')
                notify.show(`Cập nhật định mức thành công`, 'primary')
            })
        }).catch(err => {
            this.setState({
                error: true,
                message: err.response.data.message
            })
        })
    }
    handleCancel = e => {
        e.preventDefault()
        $('#createQuotas').modal('hide')
    }
    render() {
        const { error, message, version, data, mainFactor, coFactor } = this.state
        const { degrees } = this.props
        const title = "Cập nhật định mức"
        return <Modal
            modalId="updateQuotas"
            title={title}
            onSubmit={this.handleSubmit}
            onCancel={this.handleCancel}
            >
            { error && <div>
                <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                <br />
            </div> }
            <div class="form-group">
                <label class="col-sm-3 control-label">Phiên bản (*)</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" value={version || ''} onChange={e => this.setState({version: e.target.value})} required />
                </div>
            </div>
            <hr />
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-8">
                    <div class="col-sm-4 text-center">
                        <label class="control-label">Sinh viên</label>
                    </div>
                    <div class="col-sm-4 text-center">
                        <label class="control-label">Học viên cao học</label>
                    </div>
                    <div class="col-sm-4 text-center">
                        <label class="control-label">Nghiên cứu sinh</label>
                    </div>
                </div>
            </div>
            { data && data.map((d, idx) => {
                const degreeName = degrees.find(de => de.id == d.degreeId)
                return <div class="form-group" key={d.id}>
                    <label class="col-sm-3 control-label">Định mức cho {degreeName && degreeName.name} (*)</label>
                    <div class="col-sm-8">
                        <div class="col-sm-4">
                            <input type="number" min="0" class="form-control" value={d.maxStudent} placeholder={`Sinh viên`} onChange={this.handleChangeData(idx, 1)} required />
                        </div>
                        <div class="col-sm-4">
                            <input type="number" min="0" class="form-control" value={d.maxGraduated} placeholder={`Học viên cao học`} onChange={this.handleChangeData(idx, 2)} required />
                        </div>
                        <div class="col-sm-4">
                            <input type="number" min="0" class="form-control" value={d.maxResearcher} placeholder={`Nghiên cứu sinh`} onChange={this.handleChangeData(idx, 3)} required />
                        </div>
                    </div>
            </div>}) }
            <hr />
            { mainFactor && <div class="form-group">
                <label class="col-sm-3 control-label">Hệ số cho GVHD chính</label>
                <div class="col-sm-8">
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={mainFactor[0]} placeholder={`Sinh viên`} onChange={this.changeFactor(0, 'mainFactor')} />
                    </div>
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={mainFactor[1]} placeholder={`Học viên cao học`} onChange={this.changeFactor(1, 'mainFactor')} />
                    </div>
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={mainFactor[2]} placeholder={`Nghiên cứu sinh`} onChange={this.changeFactor(2, 'mainFactor')} />
                    </div>
                </div>
            </div> }
            { coFactor && <div class="form-group">
                <label class="col-sm-3 control-label">Hệ số cho GV đồng HD</label>
                <div class="col-sm-8">
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={coFactor[0]} placeholder={`Sinh viên`} onChange={this.changeFactor(0, 'coFactor')} />
                    </div>
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={coFactor[1]} placeholder={`Học viên cao học`} onChange={this.changeFactor(1, 'coFactor')} />
                    </div>
                    <div class="col-sm-4">
                        <input type="number" step="0.01" min="0" class="form-control" value={coFactor[2]} placeholder={`Nghiên cứu sinh`} onChange={this.changeFactor(2, 'coFactor')} />
                    </div>
                </div>
            </div> }
        </Modal>
    }
}

export default SuperuserQuotasUpdate
