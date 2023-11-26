import sys
import xml.dom.minidom
import mysql.connector
import json

productTitle = ""
productDescription = ""
productPrice = ""
productImage = ""
productRating = ""
productNum = sys.argv[1][0] + sys.argv[1].split('.')[0].split('/')[1]

vendor = sys.argv[2]
document = xml.dom.minidom.parse(sys.argv[1])

def getDescription(parentNode, vendor):
    global productDescription
    if parentNode.nodeType == parentNode.ELEMENT_NODE and parentNode.hasAttribute('class') and parentNode.getAttribute('class') == "seemoreParentDiv":
        return
    if parentNode.nodeType == parentNode.TEXT_NODE and vendor not in parentNode.nodeValue.upper():
        productDescription += parentNode.nodeValue
    elif parentNode.hasChildNodes():
        if parentNode.nodeType == parentNode.ELEMENT_NODE:
            attrList = parentNode.attributes
            attrs=""
            for index in range(attrList.length):
                attrs += f" {attrList.item(index).name}=\"{attrList.item(index).value}\""
            #print(f"Tag: <{parentNode.tagName}{attrs}>")
            productDescription += f"<{parentNode.tagName}{attrs}>"
        for node in parentNode.childNodes:
            getDescription(node, vendor)
        if parentNode.nodeType == parentNode.ELEMENT_NODE:
            #print(f"Tag: </{parentNode.tagName}>")
            productDescription += f"</{parentNode.tagName}>"
    elif parentNode.nodeType == parentNode.ELEMENT_NODE: # Self-closing tags
        attrList = parentNode.attributes
        attrs=""
        for index in range(attrList.length):
            attrs += f" {attrList.item(index).name}=\"{attrList.item(index).value}\""
        #print(f"Tag: <{parentNode.tagName}{attrs}/>")
        productDescription += f"<{parentNode.tagName}{attrs}/>"

if vendor == "kohls":
    # Product Title
    headerElements = document.getElementsByTagName('h1')
    for header in headerElements:
        if header.hasAttribute('class') and header.getAttribute('class') == "product-title" and header.hasChildNodes():
            node = header.childNodes[0]
            if node.nodeType == node.TEXT_NODE:
                productTitle = node.nodeValue.strip()
                break
    # Product Description
    divElements = document.getElementsByTagName('div')
    for div in divElements:
        if div.hasAttribute('id') and div.getAttribute('id') == "productDetailsTabContent" and div.hasChildNodes():
            getDescription(div, "KOHL")
            break
    # Product Price
    spanElements = document.getElementsByTagName('span')
    for span in spanElements:
        if span.hasAttribute('class') and "pdpprice-row1-reg-price" in span.getAttribute('class').split() and span.hasChildNodes():
            node = span.childNodes[0]
            if node.nodeType == node.TEXT_NODE:
                productPrice = float(node.nodeValue.split()[0][1:])
                break
    if productPrice == "":
        for span in spanElements:
            if span.hasAttribute('class') and "pdpprice-row2-main-text" in span.getAttribute('class').split() and span.hasChildNodes():
                node = span.childNodes[0]
                if node.nodeType == node.TEXT_NODE:
                    productPrice = float(node.nodeValue.split()[0][1:])
                    break
    # Product Image
    imageElements = document.getElementsByTagName('img')
    for image in imageElements:
        if image.hasAttribute('srcset'):
            productImage = image.getAttribute("srcset").split()[0]
            break
    # Product Rating
    metaElements = document.getElementsByTagName('meta')
    for meta in metaElements:
        if meta.hasAttribute('itemprop') and meta.getAttribute('itemprop') == "ratingValue" and meta.hasAttribute('content'):
            try:
                productRating = float(meta.getAttribute('content'))
            except ValueError:
                productRating = -1
            break

elif vendor == "dillards":
    # Product Title & Price
    spanElements = document.getElementsByTagName('span')
    for span in spanElements:
        category = None
        # Product Title
        if span.hasAttribute('class') and "product__title--desc" in span.getAttribute('class').split():
            category = "Title"
        # Product Price
        elif span.hasAttribute('class') and span.getAttribute('class') == "price":
            category = "Price"
        if category in ["Title", "Price"] and span.hasChildNodes():
            node = span.childNodes[0]
            if node.nodeType == node.TEXT_NODE:
                match category:
                    case "Title":
                        productTitle = node.nodeValue
                    case "Price":
                        productPrice = float(node.nodeValue[1:])
        if productTitle != "" and productPrice != "":
            break
    # Product Image
    imageElements = document.getElementsByTagName('img')
    for image in imageElements:
        if image.hasAttribute('id') and image.getAttribute('id') == "main-product-image" and image.hasAttribute('src'):
            productImage = "https:" + image.getAttribute('src')
            break
    # Product Description & Rating
    divElements = document.getElementsByTagName('div')
    for div in divElements:
        if div.hasAttribute('class') and "product-description" in div.getAttribute('class').split():
            getDescription(div, "DILLARD")
        elif div.hasAttribute('class') and div.getAttribute('class') == "starsWrapper":
            span = div.getElementsByTagName('span')[0]
            if span.hasChildNodes():
                node = span.childNodes[0]
                if node.nodeType == node.TEXT_NODE:
                    try:
                        productRating = float(node.nodeValue.strip())
                    except ValueError:
                        productRating = -1
        if productDescription != "" and productRating != "":
            break
else:
    print("Invalid vendor")

if productRating == "": # No rating was found
    productRating = -1

#print(f"Product Title: {productTitle}")
#print(f"Product Description: {productDescription}")
#print(f"Product Price: {productPrice}")
#print(f"Product Image: {productImage}")
#print(f"Product Rating: {productRating}/5")
#print(f"Name Length: {len(productTitle)}")
#print(f"Description Length: {len(productDescription)}")
#print(f"Price Length: {len(str(productPrice))}")
#print(f"Image Length: {len(productImage)}")
#print(f"Rating Length: {len(str(productRating))}")

try:
    with open("config.json", 'r') as file:
        data = json.load(file)
    cnx = mysql.connector.connect(host=data['Host'], user=data['User'], password=data['Password'], database=data['Database'])
    cursor=cnx.cursor(buffered=True)

    query = 'SELECT productId FROM Products WHERE productId = %s'
    idQuery = [productNum]
    cursor.execute(query, idQuery)
    results = cursor.fetchall()
    if cursor.rowcount == 0:
        print("Inserting record")
        query = 'INSERT INTO Products(productId, productName, productDescription, productPrice, productImage, productRating) VALUES(%s, %s, %s, %s, %s, %s)'
        cursor.execute(query, (productNum, productTitle, productDescription, productPrice, productImage, productRating))
    else:
        print("Updating record")
        query = 'UPDATE Products SET productName = %s, productDescription = %s, productPrice = %s, productImage = %s, productRating = %s WHERE productId = %s'
        cursor.execute(query, (productTitle, productDescription, productPrice, productImage, productRating, productNum))
    cnx.commit()

    cursor.close()
except mysql.connector.Error as err:
    print(err)
except IndexError:
    print("Parser error")
finally:
    try:
        cnx
    except NameError:
        pass
    else:
        cnx.close()
