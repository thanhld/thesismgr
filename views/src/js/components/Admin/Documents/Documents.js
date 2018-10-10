import React, { Component } from 'react'
import moment from 'moment'
import { formatDate } from 'Helper'
import DocumentsUpdate from './DocumentsUpdate'

class AdminDocuments extends Component {
    constructor(props) {
        super()
        this.state = {
            current_document: {}
        }
    }
    componentDidMount() {
        const { actions, documents } = this.props
        actions.loadDocuments()
    }
    onChange = document => {
        this.setState({
            current_document: document
        })
    }
    render() {
        const { actions, documents } = this.props
        const { current_document } = this.state
        return <div>
            <div class="row">
                <div class="col-xs-9 page-title">Tờ trình, quyết định</div>
            </div>
            <br />
            <div class="col-md-9 col-md-offset-1 table-responsive">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>TT</th>
                            <th class="col-xs-5">Số</th>
                            <th class="col-xs-5">Ngày</th>
                            <th class="col-xs-2">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        { documents.list.map((document, index) => (
                            <tr key={document.id}>
                                <td>{index+1}</td>
                                <td>{document.documentCode}</td>
                                <td>{moment(document.createdDate).format('DD-MM-YYYY')}</td>
                                <td>
                                    <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateDocument" onClick={e => this.onChange(document)}>
                                        Sửa
                                    </button>
                                    { document.attachment.url && <a href={document.attachment.url} class="btn btn-primary btn-margin btn-xs" download>
                                        Tải xuống
                                    </a> }
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <DocumentsUpdate
                modalId={`updateDocument`}
                document={current_document}
                actions={actions}
            />
        </div>
    }
}

export default AdminDocuments
