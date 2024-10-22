<!-- 차트 확인 Modal -->
<div class="modal fade" id="chart-modal" tabindex="-1" aria-labelledby="chartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chartModalLabel">차트 확인</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chartContainer" style="width: 100%; height: 400px;"></div>
                <div class="chart-buttons">
                    <button class="btn btn-secondary btn_viewChart" data-chart="cpa">현재 DB단가/유효DB/수익률</button>
                    <button class="btn btn-secondary btn_viewChart" data-chart="spend">지출액</button>
                    <button class="btn btn-secondary btn_viewChart" data-chart="sales">매출액</button>
                    <button class="btn btn-secondary btn_viewChart" data-chart="margin">수익</button>
                    <button class="btn btn-secondary btn_viewChart" data-chart="click">클릭수/노출수</button>
                </div>
            </div>
        </div>
    </div>
</div>