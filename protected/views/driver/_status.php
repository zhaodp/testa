Ext.create('Ext.form.Panel', {
	title: '统计',
    items: [
		{
		    xtype: 'button',
		    text: '刷新数据',
		    handler: function() {
		        //use the push() method to push another view. It works much like
		        //add() or setActiveItem(). it accepts a view instance, or you can give it
		        //a view config.
		        view.push({
		            title: 'Second',
		            html: 'Second view!'
		        });
		    }
		},
		{
			xtype: 'fieldset',
		    title: '北京',
		    instructions: '司机上线率：000',
		    items: [
		        {
		            xtype: 'textfield',
		            readOnly:true,
		            name : 'idel',
		            label: '空闲'
		        },
		        {
		            xtype: 'textfield',
		            value: 'ddd',
		            readOnly:true,
		            name : 'work',
		            label: '工作'
		        },
		        {
		            xtype: 'textfield',
		            value: 'ddd',
		            readOnly:true,
		            name : 'getoff',
		            label: '下班'
		        },
		    ]
		}    
    ]
});