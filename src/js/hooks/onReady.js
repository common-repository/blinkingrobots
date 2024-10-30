import loadScripts from '../utilities/loadScripts'
import firstInteraction from '../utilities/firstInteraction'
import updateViewportUnits from '../utilities/updateViewportUnits'
import { aload } from '../utilities/aload'
import isScrolled from '../utilities/isScrolled'

export default function onReady () {

  const $ = jQuery

  aload()
  $(document).on('firstInteraction', () => {
    aload(null, true)
  })

  isScrolled()

  firstInteraction()

  loadScripts()

  updateViewportUnits()

}
