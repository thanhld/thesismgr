import React, { Component } from 'react'
import { Link } from 'react-router'

class NotFound extends Component {
    render() {
        return (
            <div>
                <p>Không tìm thấy trang được yêu cầu.</p>
                <Link to="/">Quay lại trang chủ.</Link>
            </div>
        )
    }
}

export default NotFound
