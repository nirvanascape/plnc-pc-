<?php
	$sql = "SELECT * FROM authorised_user";
	$row = $objdb->sqlRow( $sql );
?>
<div style="margin-top:30px">
   <!-- <img src="/img/about/map_img2018.jpg"> -->
   <div id="map" style="width: 100%; height: 360px"> </div> 
</div>
<!--<TABLE border="0" cellpadding="0" cellspacing="0" id="mapinfo">
<TR>
	<TH>�ּ�</TH>
	<TD><?= $row[addr]?></TD>
</TR>
<TR>
	<TH>����ó</TH>
	<TD>TEL : <font class="b fc01"><?= $row[phone]?></font>&nbsp;&nbsp;/&nbsp;&nbsp;FAX : <?= $row[fax]?></TD>
</TR>
<TR>
	<TH>���ô� ���</TH>
	<TD><?= nl2br($row[map])?></TD>
</TR>
</TABLE>-->

<TABLE border="0" cellpadding="0" cellspacing="0" id="mapinfo">
<TR>
	<TH>�ּ�</TH>
	<TD>����Ư���� ���α� �����з� 272 �ѽ�ITŸ�� 209ȣ</TD>
</TR>
<TR>
	<TH>����ó</TH>
	<TD>TEL : <font class="b fc01"><?= $row[phone]?></font>&nbsp;&nbsp;/&nbsp;&nbsp;FAX : <?= $row[fax]?></TD>
</TR>
<TR>
	<TH>���ô� ���</TH>
	<TD>���߱��� : 2ȣ�� ���ε����д����� 3�� �ⱸ���� 900m ����(16��)�׽�Ʈ<br>
		7ȣ�� ������ 2�� �ⱸ���� 800m (15��)<br>	
	</TD>
</TR>
</TABLE>
<script>
    var map;
    
    map = new daum.maps.Map(document.getElementById('map'), {
        center : new daum.maps.LatLng(37.482547, 126.894301)
    });
    var marker = new daum.maps.Marker({
        position : new daum.maps.LatLng(37.482547, 126.894301)
    });
    marker.setMap(map);
</script>